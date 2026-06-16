<?php

namespace App\Http\Controllers;

use App\Models\ShipmentCalendar;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ShipmentCalendarsController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = ShipmentCalendar::query()->with('salesContract')->latest();

            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('laycan_start', fn ($row) => $row->laycan_start?->format('Y-m-d'))
                ->editColumn('laycan_end', fn ($row) => $row->laycan_end?->format('Y-m-d') ?: '-')
                ->editColumn('eta', fn ($row) => $row->eta?->format('Y-m-d') ?: '-')
                ->editColumn('vessel', fn ($row) => $this->displayVessel($row))
                ->editColumn('qty', fn ($row) => number_format((float) $row->qty, 2))
                ->addColumn('laycan_period', function ($row) {
                    $start = $row->laycan_start?->format('d M Y');
                    $end = $row->laycan_end?->format('d M Y');

                    return $end ? "{$start} - {$end}" : $start;
                })
                ->addColumn('status_badge', function ($row) {
                    $classes = [
                        'Confirmed' => 'bg-sky-100 text-sky-700',
                        'Loading' => 'bg-amber-100 text-amber-700',
                        'Complete' => 'bg-green-100 text-green-700',
                    ];

                    $status = $this->displayStatus($row);
                    $class = $classes[$status] ?? 'bg-slate-100 text-slate-700';

                    return '<span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold ' . $class . '">' . e($status) . '</span>';
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                class="btn-edit-calendar inline-flex items-center rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-dark"
                                data-id="' . $row->id . '"
                                data-buyer="' . e($row->buyer) . '"
                                data-contract_no="' . e($row->contract_no) . '"
                                data-laycan_start="' . e($row->laycan_start?->format('Y-m-d')) . '"
                                data-laycan_end="' . e($row->laycan_end?->format('Y-m-d')) . '"
                                data-eta="' . e($row->eta?->format('Y-m-d')) . '"
                                data-vessel="' . e($this->displayVessel($row)) . '"
                                data-qty="' . e($row->qty) . '"
                                data-spec="' . e($row->spec) . '"
                                data-laycan_status="' . e($this->displayStatus($row)) . '"
                                data-discharge_port="' . e($row->discharge_port) . '">
                                Edit
                            </button>

                            <form action="' . route('calendar.destroy', $row->id) . '" method="POST" class="delete-form inline">
                                ' . csrf_field() . method_field('DELETE') . '
                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-lg bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                                    Delete
                                </button>
                            </form>
                        </div>
                    ';
                })
                ->rawColumns(['status_badge', 'action'])
                ->make(true);
        }

        return view('calendar.index');
    }

    public function events()
    {
        $colors = [
            'Confirmed' => ['background' => '#0369a1', 'border' => '#075985'],
            'Loading' => ['background' => '#d97706', 'border' => '#b45309'],
            'Complete' => ['background' => '#15803d', 'border' => '#166534'],
        ];

        return ShipmentCalendar::query()
            ->with('salesContract')
            ->orderBy('laycan_start')
            ->get()
            ->map(function ($row) use ($colors) {
                $status = $this->displayStatus($row);
                $vessel = $this->displayVessel($row);
                $color = $colors[$status] ?? $colors['Confirmed'];

                return [
                    'id' => $row->id,
                    'title' => "{$row->buyer} - {$vessel}",
                    'start' => $row->laycan_start?->format('Y-m-d'),
                    'end' => $row->laycan_end ? $row->laycan_end->copy()->addDay()->format('Y-m-d') : null,
                    'backgroundColor' => $color['background'],
                    'borderColor' => $color['border'],
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'buyer' => $row->buyer,
                        'contract_no' => $row->contract_no,
                        'eta' => $row->eta?->format('Y-m-d'),
                        'vessel' => $vessel,
                        'qty' => number_format((float) $row->qty, 2),
                        'spec' => $row->spec,
                        'laycan_status' => $status,
                        'discharge_port' => $row->discharge_port,
                    ],
                ];
            });
    }

    private function displayVessel(ShipmentCalendar $calendar): string
    {
        $contract = $calendar->salesContract;

        if (! $contract) {
            return $calendar->vessel ?: 'TBA';
        }

        return collect([
            $contract->tug_boat_name,
            $contract->barge_vessel_name,
        ])->filter()->implode(' / ') ?: ($calendar->vessel ?: $contract->shipment_no ?: 'TBA');
    }

    private function displayStatus(ShipmentCalendar $calendar): string
    {
        if ($calendar->laycan_status === 'Complete' && $calendar->laycan_start?->isFuture()) {
            return 'Confirmed';
        }

        return $calendar->laycan_status ?: 'Confirmed';
    }

    public function store(Request $request)
    {
        ShipmentCalendar::create($this->validated($request));

        return response()->json(['success' => true]);
    }

    public function update(Request $request, ShipmentCalendar $calendar)
    {
        $calendar->update($this->validated($request));

        return response()->json(['success' => true]);
    }

    public function destroy(ShipmentCalendar $calendar)
    {
        $calendar->delete();

        return response()->json(['success' => true]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'buyer' => ['required', 'string', 'max:255'],
            'contract_no' => ['required', 'string', 'max:255'],
            'laycan_start' => ['required', 'date'],
            'laycan_end' => ['nullable', 'date', 'after_or_equal:laycan_start'],
            'eta' => ['nullable', 'date'],
            'vessel' => ['required', 'string', 'max:255'],
            'qty' => ['required', 'numeric', 'min:0'],
            'spec' => ['nullable', 'string', 'max:255'],
            'laycan_status' => ['required', Rule::in(['Confirmed', 'Loading', 'Complete'])],
            'discharge_port' => ['required', 'string', 'max:255'],
        ]);
    }
}
