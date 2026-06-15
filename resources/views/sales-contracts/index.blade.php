@extends('layouts.master')

@section('content')
  <div class="space-y-4">
    <div class="bg-white shadow-sm">
      <div class="flex flex-col gap-3 border-b border-slate-200 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-slate-800">Sales Order List</h1>
          <p class="mt-1 text-sm text-slate-500">Submitted sales contracts and their laycan calendar integration status.</p>
        </div>

        <a href="{{ route('sales-contracts.create') }}"
          class="inline-flex items-center justify-center gap-2 bg-teal-700 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-800">
          <i class="fas fa-plus text-xs"></i>
          New Sales Order
        </a>
      </div>
    </div>

    <div class="bg-white p-5 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1100px] text-sm">
          <thead>
            <tr class="border-b border-slate-300 text-left text-slate-700">
              <th class="px-3 py-3 font-semibold">Contract No</th>
              <th class="px-3 py-3 font-semibold">Buyer</th>
              <th class="px-3 py-3 font-semibold">Entity</th>
              <th class="px-3 py-3 font-semibold">Market</th>
              <th class="px-3 py-3 font-semibold">Commodity</th>
              <th class="px-3 py-3 font-semibold">Qty</th>
              <th class="px-3 py-3 font-semibold">Laycan</th>
              <th class="px-3 py-3 font-semibold">Status</th>
              <th class="px-3 py-3 font-semibold">Calendar</th>
              <th class="px-3 py-3 font-semibold">Attachment</th>
              <th class="px-3 py-3 font-semibold">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($contracts as $contract)
              @php
                $approvalTotal = $contract->approvals->count();
                $approvedTotal = $contract->approvals->where('status', 'Approved')->count();
                $displayStatus = $approvalTotal > 0 && $approvedTotal === $approvalTotal
                    ? 'Revision Approved'
                    : ($contract->final_status ?? $contract->draft_status);
                $statusClass = match ($displayStatus) {
                    'Revision Approved' => 'bg-green-100 text-green-700',
                    'On Hold' => 'bg-amber-100 text-amber-700',
                    'Wait for Approval', 'Pending Approval', 'Request Sign' => 'bg-sky-100 text-sky-700',
                    'Cancelled' => 'bg-red-100 text-red-700',
                    default => 'bg-slate-100 text-slate-700',
                };
              @endphp
              <tr class="border-b border-slate-100 text-slate-700 hover:bg-slate-50">
                <td class="px-3 py-3 font-semibold text-slate-900">{{ $contract->contract_number }}</td>
                <td class="px-3 py-3">{{ $contract->buyer_name ?: '-' }}</td>
                <td class="px-3 py-3">{{ $contract->seller_entity ?: '-' }}</td>
                <td class="px-3 py-3">{{ $contract->market_type ?: '-' }}</td>
                <td class="px-3 py-3">{{ $contract->commodity ?: '-' }}</td>
                <td class="px-3 py-3">
                  @if ($contract->sales_quantity_mt || $contract->contract_quantity_mt)
                    {{ number_format((float) ($contract->sales_quantity_mt ?? $contract->contract_quantity_mt), 2) }} MT
                  @else
                    <span class="text-slate-400">-</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  @if ($contract->laycan_start_date)
                    {{ $contract->laycan_start_date->format('d M Y') }}
                    @if ($contract->laycan_end_date)
                      - {{ $contract->laycan_end_date->format('d M Y') }}
                    @endif
                  @else
                    <span class="text-slate-400">Not set</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $statusClass }}">
                    {{ $displayStatus }}
                  </span>
                </td>
                <td class="px-3 py-3">
                  @if ($contract->shipmentCalendar)
                    <a href="{{ route('calendar.index') }}" class="inline-flex bg-teal-100 px-2 py-1 text-xs font-semibold text-teal-800 hover:bg-teal-200">
                      Synced
                    </a>
                  @elseif ($contract->laycan_start_date)
                    <span class="inline-flex bg-amber-100 px-2 py-1 text-xs font-semibold text-amber-800">Pending</span>
                  @else
                    <span class="inline-flex bg-slate-100 px-2 py-1 text-xs font-semibold text-slate-500">No laycan</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  @if ($contract->contract_file_path)
                    <a href="{{ route('sales-contracts.contract-file', $contract) }}" target="_blank"
                      class="text-teal-700 hover:text-teal-900 hover:underline">
                      {{ $contract->stamped_contract_file_path ? 'Stamped PDF' : ($contract->contract_file_name ?? 'Open file') }}
                    </a>
                  @else
                    <span class="text-slate-400">No file</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('sales-contracts.show', $contract) }}"
                      class="inline-flex items-center bg-slate-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                      View
                    </a>

                    <a href="{{ route('sales-contracts.edit', $contract) }}"
                      class="inline-flex items-center bg-teal-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-800">
                      Edit
                    </a>

                    <form method="POST" action="{{ route('sales-contracts.destroy', $contract) }}"
                      onsubmit="return confirm('Delete this sales order?')" class="inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit"
                        class="inline-flex items-center bg-red-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-red-700">
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="11" class="px-3 py-10 text-center text-slate-500">
                  No sales order submitted yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-5">
        {{ $contracts->links() }}
      </div>
    </div>
  </div>
@endsection
