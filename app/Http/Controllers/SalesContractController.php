<?php

namespace App\Http\Controllers;

use App\Models\SalesContract;
use App\Models\SalesContractApproval;
use App\Models\ShipmentCalendar;
use App\Models\User;
use App\Services\PdfApprovalStamper;
use App\Support\SalesApprovalRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SalesContractController extends Controller
{
    public function index()
    {
        $contracts = SalesContract::query()
            ->with('shipmentCalendar')
            ->latest()
            ->paginate(15);

        return view('sales-contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('sales-contracts.create', $this->formData());
    }

    public function edit(SalesContract $salesContract)
    {
        return view('sales-contracts.create', $this->formData($salesContract));
    }

    public function show(SalesContract $salesContract)
    {
        $this->ensureApprovalRoute($salesContract);
        $salesContract->load(['approvals', 'shipmentCalendar']);

        return view('sales-contracts.show', compact('salesContract'));
    }

    public function contractFile(SalesContract $salesContract, PdfApprovalStamper $stamper)
    {
        $this->refreshStampedPdfIfNeeded($salesContract, $stamper);
        $salesContract->refresh();

        $path = $salesContract->stamped_contract_file_path ?: $salesContract->contract_file_path;

        abort_unless($path && Storage::disk('public')->exists($path), 404);

        $name = $salesContract->stamped_contract_file_name
            ?: $salesContract->contract_file_name
            ?: basename($path);

        return response()->file(Storage::disk('public')->path($path), [
            'Content-Disposition' => 'inline; filename="' . addslashes($name) . '"',
        ]);
    }

    private function refreshStampedPdfIfNeeded(SalesContract $salesContract, PdfApprovalStamper $stamper): void
    {
        if (! $salesContract->contract_file_path || strtolower(pathinfo($salesContract->contract_file_path, PATHINFO_EXTENSION)) !== 'pdf') {
            return;
        }

        $latestApprovalAt = $salesContract->approvals()
            ->where('status', 'Approved')
            ->max('approved_at');

        if (! $latestApprovalAt) {
            return;
        }

        if (! $salesContract->stamped_contract_file_path || ! Storage::disk('public')->exists($salesContract->stamped_contract_file_path)) {
            $stamper->stamp($salesContract);
            return;
        }

        $stampedModifiedAt = Storage::disk('public')->lastModified($salesContract->stamped_contract_file_path);

        if (strtotime($latestApprovalAt) > $stampedModifiedAt) {
            $stamper->stamp($salesContract);
        }
    }

    private function formData(?SalesContract $salesContract = null): array
    {
        $buyers = SalesContract::query()
            ->whereNotNull('buyer_name')
            ->distinct()
            ->orderBy('buyer_name')
            ->pluck('buyer_name');

        $recentContracts = SalesContract::query()
            ->latest()
            ->take(6)
            ->get(['contract_number', 'buyer_name', 'draft_status', 'final_status', 'created_at']);

        return compact('buyers', 'recentContracts', 'salesContract');
    }

    public function store(Request $request)
    {
        $payload = $this->payloadFromRequest($request);

        DB::transaction(function () use ($payload) {
            $salesContract = SalesContract::create($payload);
            $this->ensureApprovalRoute($salesContract);
            $this->syncShipmentCalendar($salesContract);
        });

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', 'Sales contract saved successfully');
    }

    public function update(Request $request, SalesContract $salesContract)
    {
        $payload = $this->payloadFromRequest($request, $salesContract);

        DB::transaction(function () use ($salesContract, $payload) {
            $salesContract->update($payload);
            $freshContract = $salesContract->fresh();
            $this->ensureApprovalRoute($freshContract);
            $this->syncShipmentCalendar($freshContract);
        });

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', 'Sales contract updated successfully');
    }

    public function destroy(SalesContract $salesContract)
    {
        DB::transaction(function () use ($salesContract) {
            $salesContract->shipmentCalendar?->delete();

            if ($salesContract->contract_file_path) {
                Storage::disk('public')->delete($salesContract->contract_file_path);
            }

            if ($salesContract->stamped_contract_file_path) {
                Storage::disk('public')->delete($salesContract->stamped_contract_file_path);
            }

            $salesContract->delete();
        });

        return redirect()
            ->route('sales-contracts.index')
            ->with('success', 'Sales contract deleted successfully');
    }

    private function payloadFromRequest(Request $request, ?SalesContract $salesContract = null): array
    {
        $payload = array_replace($this->emptyPayload(), $this->validated($request, $salesContract));
        $payload['pricing_basis'] = 'ICI';
        $payload['contract_number'] = $payload['contract_number'] ?: ($salesContract?->contract_number ?: $this->generateContractNumber());
        $payload['draft_status'] = $payload['draft_status'] ?: 'Draft';
        $payload['price_currency'] = ($payload['market_type'] ?? null) === 'Export' ? 'USD' : 'IDR';

        if (($payload['price_type'] ?? null) === 'Formula') {
            $payload['fixed_price'] = null;
        } else {
            $payload['formula_price'] = null;
        }

        if ($request->hasFile('contract_file')) {
            if ($salesContract?->contract_file_path) {
                Storage::disk('public')->delete($salesContract->contract_file_path);
            }

            if ($salesContract?->stamped_contract_file_path) {
                Storage::disk('public')->delete($salesContract->stamped_contract_file_path);
            }

            $file = $request->file('contract_file');
            $payload['contract_file_path'] = $file->store('contracts', 'public');
            $payload['contract_file_name'] = $file->getClientOriginalName();
            $payload['stamped_contract_file_path'] = null;
            $payload['stamped_contract_file_name'] = null;
        } elseif ($salesContract) {
            $payload['contract_file_path'] = $salesContract->contract_file_path;
            $payload['contract_file_name'] = $salesContract->contract_file_name;
            $payload['stamped_contract_file_path'] = $salesContract->stamped_contract_file_path;
            $payload['stamped_contract_file_name'] = $salesContract->stamped_contract_file_name;
        }

        return $payload;
    }

    private function syncShipmentCalendar(SalesContract $salesContract): void
    {
        if (! $salesContract->laycan_start_date) {
            $salesContract->shipmentCalendar?->delete();
            return;
        }

        ShipmentCalendar::updateOrCreate(
            ['sales_contract_id' => $salesContract->id],
            [
                'buyer' => $salesContract->buyer_name ?: 'TBA',
                'contract_no' => $salesContract->contract_number,
                'laycan_start' => $salesContract->laycan_start_date,
                'laycan_end' => $salesContract->laycan_end_date,
                'eta' => $salesContract->eta,
                'vessel' => $this->calendarVessel($salesContract),
                'qty' => $salesContract->sales_quantity_mt
                    ?? $salesContract->contract_quantity_mt
                    ?? 0,
                'spec' => $this->calendarSpec($salesContract),
                'laycan_status' => $this->calendarStatus($salesContract),
                'discharge_port' => $salesContract->destination_port ?: 'TBA',
            ]
        );
    }

    private function ensureApprovalRoute(SalesContract $salesContract): void
    {
        $users = User::whereIn('username', SalesApprovalRoute::usernames())
            ->get()
            ->keyBy('username');

        foreach (SalesApprovalRoute::approvers() as $approver) {
            SalesContractApproval::firstOrCreate(
                [
                    'sales_contract_id' => $salesContract->id,
                    'approver_username' => $approver['username'],
                ],
                [
                    'user_id' => $users->get($approver['username'])?->id,
                    'approval_order' => $approver['order'],
                    'approval_stage' => $approver['stage'],
                    'approval_group' => $approver['group'],
                    'position' => $approver['position'],
                    'approver_name' => $approver['name'],
                    'status' => 'Pending',
                ]
            );
        }
    }

    private function calendarVessel(SalesContract $salesContract): string
    {
        return $salesContract->barge_vessel_name
            ?: $salesContract->tug_boat_name
            ?: $salesContract->shipment_no
            ?: 'TBA';
    }

    private function calendarSpec(SalesContract $salesContract): ?string
    {
        return collect([
            $salesContract->gar_gcv ? 'GAR/GCV ' . $salesContract->gar_gcv : null,
            $salesContract->actual_gar ? 'Actual ' . $salesContract->actual_gar : null,
            $salesContract->commodity,
        ])->filter()->implode(' - ') ?: null;
    }

    private function calendarStatus(SalesContract $salesContract): string
    {
        return 'Confirmed';
    }

    private function generateContractNumber(): string
    {
        do {
            $contractNumber = 'SO-' . now()->format('Ymd-His') . '-' . Str::upper(Str::random(4));
        } while (SalesContract::where('contract_number', $contractNumber)->exists());

        return $contractNumber;
    }

    private function emptyPayload(): array
    {
        return [
            'contract_number' => null,
            'buyer_name' => null,
            'buyer_reference' => null,
            'seller_entity' => null,
            'market_type' => null,
            'pic_marketing' => null,
            'submission_date' => null,
            'submitted_by' => null,
            'draft_status' => null,
            'commodity' => null,
            'contract_quantity_mt' => null,
            'sales_quantity_mt' => null,
            'shipment_period' => null,
            'incoterms' => null,
            'gar_gcv' => null,
            'actual_gar' => null,
            'total_moisture' => null,
            'inherent_moisture' => null,
            'ash' => null,
            'ash_limit' => null,
            'sulphur' => null,
            'sulphur_limit' => null,
            'size' => null,
            'price_type' => null,
            'fixed_price' => null,
            'formula_price' => null,
            'minus_plus' => null,
            'payment_term_summary' => null,
            'shipment_no' => null,
            'barges' => null,
            'eta' => null,
            'laycan_start_date' => null,
            'laycan_end_date' => null,
            'load_port' => null,
            'destination_port' => null,
            'tug_boat_name' => null,
            'barge_vessel_name' => null,
            'barge_vessel_agent' => null,
            'dmo_status' => null,
            'surveyor' => null,
            'laycan_status' => null,
            'approval_status' => null,
            'approval_date' => null,
            'revision_note' => null,
            'final_status' => null,
            'stamped_contract_file_path' => null,
            'stamped_contract_file_name' => null,
        ];
    }

    private function validated(Request $request, ?SalesContract $salesContract = null): array
    {
        return $request->validate([
            'contract_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('sales_contracts', 'contract_number')->ignore($salesContract?->id),
            ],
            'buyer_name' => ['nullable', 'string', 'max:255'],
            'buyer_reference' => ['nullable', 'string', 'max:255'],
            'seller_entity' => ['nullable', Rule::in(['PMC', 'IBPE', 'APE'])],
            'market_type' => ['nullable', Rule::in(['Domestic', 'Export'])],
            'pic_marketing' => ['nullable', 'string', 'max:255'],
            'submission_date' => ['nullable', 'date'],
            'submitted_by' => ['nullable', 'string', 'max:255'],
            'draft_status' => ['nullable', Rule::in(['Draft', 'Under Review', 'Pending Approval', 'Confirmed', 'Cancelled'])],

            'commodity' => ['nullable', Rule::in(['Cooking Indonesian Origin', 'Non Cooking Indonesian Origin'])],
            'contract_quantity_mt' => ['nullable', 'numeric', 'min:0'],
            'sales_quantity_mt' => ['nullable', 'numeric', 'min:0'],
            'shipment_period' => ['nullable', 'string', 'max:30'],
            'incoterms' => ['nullable', Rule::in(['FOB', 'CIF'])],

            'gar_gcv' => ['nullable', Rule::in(['2700', '2800', '3000', '3500'])],
            'actual_gar' => ['nullable', 'string', 'max:255'],
            'total_moisture' => ['nullable', 'string', 'max:255'],
            'inherent_moisture' => ['nullable', 'string', 'max:255'],
            'ash' => ['nullable', 'string', 'max:255'],
            'ash_limit' => ['nullable', 'string', 'max:255'],
            'sulphur' => ['nullable', 'string', 'max:255'],
            'sulphur_limit' => ['nullable', 'string', 'max:255'],
            'size' => ['nullable', Rule::in(['No Sizing', 'Sizing'])],

            'price_type' => ['nullable', Rule::in(['Fixed Price', 'Formula'])],
            'fixed_price' => ['nullable', 'numeric', 'min:0'],
            'formula_price' => ['nullable', 'string'],
            'minus_plus' => ['nullable', 'numeric'],
            'payment_term_summary' => ['nullable', 'string'],

            'shipment_no' => ['nullable', 'string', 'max:255'],
            'barges' => ['nullable', Rule::in(['FOB Barge', 'FOB MV GNG', 'FOB MV Gearless'])],
            'eta' => ['nullable', 'date'],
            'laycan_start_date' => ['nullable', 'date'],
            'laycan_end_date' => ['nullable', 'date', 'after_or_equal:laycan_start_date'],
            'load_port' => ['nullable', 'string', 'max:255'],
            'destination_port' => ['nullable', 'string', 'max:255'],
            'tug_boat_name' => ['nullable', 'string', 'max:255'],
            'barge_vessel_name' => ['nullable', 'string', 'max:255'],
            'barge_vessel_agent' => ['nullable', 'string', 'max:255'],
            'dmo_status' => ['nullable', Rule::in(['DMO', 'Non DMO'])],
            'surveyor' => ['nullable', 'string', 'max:255'],
            'laycan_status' => ['nullable', Rule::in(['Confirm', 'Nego Laycan'])],

            'approval_status' => ['nullable', Rule::in(['Request Sign', 'Half Signed', 'Full Signed'])],
            'approval_date' => ['nullable', 'date'],
            'revision_note' => ['nullable', 'string'],
            'final_status' => ['nullable', Rule::in(['Wait for Approval', 'On Hold', 'Revision Approved'])],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ]);
    }
}
