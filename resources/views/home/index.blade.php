@extends('layouts.master')

@section('content')
  <div class="mx-auto w-full max-w-screen-2xl px-4 py-4 sm:px-6 lg:px-8">

    {{-- Container kosong (siap diisi) --}}
    <div class="border-border bg-surface rounded-2xl border p-4 shadow-sm">
      <h2 class="text-text text-lg font-semibold">Recent Activity</h2>

      <div class="mt-4 overflow-x-auto">
        <div class="border-border bg-surface text-muted rounded-2xl border p-6 text-center shadow-sm">
          No data yet
        </div>
      </div>

    </div>

  </div>
@endsection
