<?php

namespace App\Http\Controllers;

use App\Models\SalesContract;
use App\Models\ShipmentCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesContractController extends Controller
{
    public function index()
    {
        return redirect()->route('sales-contracts.create');
    }

    public function create()
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

        return view('sales-contracts.create', compact('buyers', 'recentContracts'));
    }

    public function store(Request $request)
    {
        $payload = $this->validated($request);
        $payload['pricing_basis'] = 'ICI';
        $payload['price_currency'] = $payload['market_type'] === 'Export' ? 'USD' : 'IDR';

        if (($payload['price_type'] ?? null) === 'Formula') {
            $payload['fixed_price'] = null;
        } else {
            $payload['formula_price'] = null;
        }

        if ($request->hasFile('contract_file')) {
            $file = $request->file('contract_file');
            $payload['contract_file_path'] = $file->store('contracts', 'public');
            $payload['contract_file_name'] = $file->getClientOriginalName();
        }

        DB::transaction(function () use ($payload) {
            $salesContract = SalesContract::create($payload);
            $this->syncShipmentCalendar($salesContract);
        });

        return redirect()
            ->route('sales-contracts.create')
            ->with('success', 'Sales contract saved successfully');
    }

    private function syncShipmentCalendar(SalesContract $salesContract): void
    {
        if (! $salesContract->laycan_start_date) {
            return;
        }

        ShipmentCalendar::updateOrCreate(
            ['sales_contract_id' => $salesContract->id],
            [
                'buyer' => $salesContract->buyer_name,
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
        return match ($salesContract->final_status) {
            'Loading' => 'Loading',
            'Complete' => 'Complete',
            default => 'Confirmed',
        };
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'contract_number' => ['required', 'string', 'max:255', 'unique:sales_contracts,contract_number'],
            'buyer_name' => ['required', 'string', 'max:255'],
            'buyer_reference' => ['nullable', 'string', 'max:255'],
            'seller_entity' => ['required', Rule::in(['PMC', 'IBPE', 'APE'])],
            'market_type' => ['required', Rule::in(['Domestic', 'Export'])],
            'pic_marketing' => ['nullable', 'string', 'max:255'],
            'submission_date' => ['nullable', 'date'],
            'submitted_by' => ['nullable', 'string', 'max:255'],
            'draft_status' => ['required', Rule::in(['Draft', 'Under Review', 'Pending Approval', 'Confirmed', 'Cancelled'])],

            'commodity' => ['required', Rule::in(['Cooking Indonesian Origin', 'Non Cooking Indonesian Origin'])],
            'contract_quantity_mt' => ['nullable', 'numeric', 'min:0'],
            'sales_quantity_mt' => ['nullable', 'numeric', 'min:0'],
            'shipment_period' => ['nullable', 'date_format:Y-m'],
            'incoterms' => ['required', Rule::in(['FOB', 'CIF'])],

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
            'fixed_price' => ['nullable', 'required_if:price_type,Fixed Price', 'numeric', 'min:0'],
            'formula_price' => ['nullable', 'required_if:price_type,Formula', 'string'],
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

            'approval_status' => ['nullable', Rule::in(['Half Signed', 'Full Signed'])],
            'approval_date' => ['nullable', 'date'],
            'revision_note' => ['nullable', 'string'],
            'final_status' => ['nullable', Rule::in(['Confirmed', 'Loading', 'On Hold', 'Revision', 'Cancelled', 'Complete'])],
            'contract_file' => ['nullable', 'file', 'mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png', 'max:10240'],
        ]);
    }
}
