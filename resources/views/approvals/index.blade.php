@extends('layouts.master')

@section('content')
  <div class="space-y-4">
    <div class="bg-white shadow-sm">
      <div class="border-b border-slate-200 px-5 py-4">
        <h1 class="text-2xl font-semibold text-slate-800">Approval Queue</h1>
        <p class="mt-1 text-sm text-slate-500">Sales orders waiting for your approval.</p>
      </div>
    </div>

    <div class="bg-white p-5 shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[1000px] text-sm">
          <thead>
            <tr class="border-b border-slate-300 text-left text-slate-700">
              <th class="px-3 py-3 font-semibold">Contract No</th>
              <th class="px-3 py-3 font-semibold">Buyer</th>
              <th class="px-3 py-3 font-semibold">Your Role</th>
              <th class="px-3 py-3 font-semibold">Approval Status</th>
              <th class="px-3 py-3 font-semibold">Approved At</th>
              <th class="px-3 py-3 font-semibold">Final Gate</th>
              <th class="px-3 py-3 font-semibold">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($approvals as $approval)
              @php
                $initialPending = $approval->salesContract->approvals
                    ->where('approval_stage', 'initial')
                    ->where('status', '!=', 'Approved')
                    ->count();
                $isLocked = $approval->approval_stage === 'final' && $initialPending > 0;
              @endphp
              <tr class="border-b border-slate-100 text-slate-700 hover:bg-slate-50">
                <td class="px-3 py-3 font-semibold text-slate-900">{{ $approval->salesContract->contract_number }}</td>
                <td class="px-3 py-3">{{ $approval->salesContract->buyer_name ?: '-' }}</td>
                <td class="px-3 py-3">
                  <div class="font-semibold text-slate-800">{{ $approval->position }}</div>
                  <div class="text-xs text-slate-500">{{ $approval->approval_group }}</div>
                </td>
                <td class="px-3 py-3">
                  <span class="inline-flex px-2 py-1 text-xs font-semibold {{ $approval->status === 'Approved' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                    {{ $approval->status }}
                  </span>
                </td>
                <td class="px-3 py-3">
                  {{ $approval->approved_at?->format('d M Y H:i') ?? '-' }}
                </td>
                <td class="px-3 py-3">
                  @if ($approval->approval_stage === 'final')
                    @if ($isLocked)
                      <span class="text-amber-700">{{ $initialPending }} initial approval pending</span>
                    @else
                      <span class="text-green-700">Open</span>
                    @endif
                  @else
                    <span class="text-slate-400">Not required</span>
                  @endif
                </td>
                <td class="px-3 py-3">
                  <div class="flex items-center gap-2">
                    <a href="{{ route('sales-contracts.show', $approval->salesContract) }}"
                      class="inline-flex bg-slate-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-slate-800">
                      View
                    </a>
                    <form method="POST" action="{{ route('approvals.approve', $approval) }}">
                      @csrf
                      <button type="submit" @disabled($approval->status === 'Approved' || $isLocked)
                        class="inline-flex bg-teal-700 px-3 py-2 text-xs font-semibold text-white transition hover:bg-teal-800 disabled:cursor-not-allowed disabled:bg-slate-300">
                        Approve
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-3 py-10 text-center text-slate-500">
                  No approval task found for your account.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>

      <div class="mt-5">
        {{ $approvals->links() }}
      </div>
    </div>
  </div>
@endsection
