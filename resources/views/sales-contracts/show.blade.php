@extends('layouts.master')

@section('content')
  <div class="space-y-4">
    <div class="bg-white shadow-sm">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-slate-800">Sales Order Detail</h1>
          <p class="mt-1 text-sm text-slate-500">{{ $salesContract->contract_number }}</p>
        </div>
        <div class="flex gap-2">
          <a href="{{ route('sales-contracts.index') }}"
            class="inline-flex items-center bg-slate-100 px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
            Back
          </a>
          <a href="{{ route('sales-contracts.edit', $salesContract) }}"
            class="inline-flex items-center bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
            Edit
          </a>
        </div>
      </div>
    </div>

    <div class="grid gap-4 xl:grid-cols-3">
      <div class="bg-white p-5 shadow-sm xl:col-span-2">
        <h2 class="border-b border-slate-300 pb-2 text-base font-bold text-slate-800">Contract Information</h2>
        <dl class="mt-4 grid gap-x-8 gap-y-3 text-sm md:grid-cols-2">
          @foreach ([
              'Buyer' => $salesContract->buyer_name,
              'Buyer Reference' => $salesContract->buyer_reference,
              'Seller Entity' => $salesContract->seller_entity,
              'Market Type' => $salesContract->market_type,
              'PIC Marketing' => $salesContract->pic_marketing,
              'Submitted By' => $salesContract->submitted_by,
              'Draft Status' => $salesContract->draft_status,
              'Approval Status' => $salesContract->approval_status,
              'Final Status' => $salesContract->final_status,
              'Commodity' => $salesContract->commodity,
              'Contract Quantity' => $salesContract->contract_quantity_mt ? number_format((float) $salesContract->contract_quantity_mt, 2) . ' MT' : null,
              'Sales Quantity' => $salesContract->sales_quantity_mt ? number_format((float) $salesContract->sales_quantity_mt, 2) . ' MT' : null,
              'Shipment Period' => $salesContract->shipment_period,
              'Incoterms' => $salesContract->incoterms,
              'GAR / GCV' => $salesContract->gar_gcv,
              'Actual GAR' => $salesContract->actual_gar,
              'ETA' => $salesContract->eta?->format('d M Y'),
              'Laycan' => $salesContract->laycan_start_date ? $salesContract->laycan_start_date->format('d M Y') . ($salesContract->laycan_end_date ? ' - ' . $salesContract->laycan_end_date->format('d M Y') : '') : null,
              'Barge / Vessel' => $salesContract->barge_vessel_name,
              'Destination Port' => $salesContract->destination_port,
          ] as $label => $value)
            <div class="grid grid-cols-[150px_1fr] gap-3">
              <dt class="text-slate-500">{{ $label }}</dt>
              <dd class="font-medium text-slate-800">{{ $value ?: '-' }}</dd>
            </div>
          @endforeach
        </dl>
      </div>

      <div class="bg-white p-5 shadow-sm">
        <h2 class="border-b border-slate-300 pb-2 text-base font-bold text-slate-800">Attachment</h2>
        <div class="mt-4 text-sm">
          @if ($salesContract->contract_file_path)
            <a href="{{ asset('storage/' . $salesContract->contract_file_path) }}" target="_blank"
              class="font-semibold text-teal-700 hover:text-teal-900 hover:underline">
              {{ $salesContract->contract_file_name ?? 'Open contract file' }}
            </a>
          @else
            <span class="text-slate-500">No file uploaded.</span>
          @endif
        </div>
      </div>
    </div>

    <div class="bg-white p-5 shadow-sm">
      <h2 class="border-b border-slate-300 pb-2 text-base font-bold text-slate-800">Approval Route Log</h2>
      <div class="mt-4 overflow-x-auto">
        <table class="w-full min-w-[900px] text-sm">
          <thead>
            <tr class="border-b border-slate-300 text-left text-slate-700">
              <th class="px-3 py-3 font-semibold">No</th>
              <th class="px-3 py-3 font-semibold">Group</th>
              <th class="px-3 py-3 font-semibold">Position</th>
              <th class="px-3 py-3 font-semibold">Approver</th>
              <th class="px-3 py-3 font-semibold">Status</th>
              <th class="px-3 py-3 font-semibold">Approved Date & Time</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($salesContract->approvals as $approval)
              <tr class="border-b border-slate-100 text-slate-700">
                <td class="px-3 py-3">{{ $approval->approval_order }}</td>
                <td class="px-3 py-3">{{ $approval->approval_group }}</td>
                <td class="px-3 py-3">{{ $approval->position }}</td>
                <td class="px-3 py-3 font-semibold text-slate-800">{{ $approval->approver_name }}</td>
                <td class="px-3 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $approval->status === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $approval->status }}
                  </span>
                </td>
                <td class="px-3 py-3">{{ $approval->approved_at?->format('d M Y H:i:s') ?? '-' }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
