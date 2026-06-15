@extends('layouts.master')

@php
  $activities = [
      [
          'title' => 'For Release',
          'tiles' => [
              ['label' => 'Sales Quotes - Open', 'value' => '0'],
              ['label' => 'Sales Orders - Open', 'value' => '0'],
          ],
      ],
      [
          'title' => 'Sales Orders Released Not Shipped',
          'tiles' => [
              ['label' => 'Ready To Ship', 'value' => '0'],
              ['label' => 'Partially Shipped', 'value' => '0'],
              ['label' => 'Delayed', 'value' => '0'],
              ['label' => 'Average Days Delayed', 'value' => '0', 'tone' => 'muted'],
          ],
      ],
      [
          'title' => 'Returns',
          'tiles' => [
              ['label' => 'Sales Return O... - Open', 'value' => '0'],
              ['label' => 'Sales Credit M... - Open', 'value' => '0'],
          ],
      ],
  ];

  $sections = [
      [
          'title' => 'User Tasks',
          'subtitle' => 'My User Tasks',
          'tiles' => [
              ['label' => 'Pending User Tasks', 'value' => '0'],
          ],
      ],
      [
          'title' => 'Email Status',
          'subtitle' => 'Email Activities',
          'wide' => true,
          'tiles' => [
              ['label' => 'Failed Emails in Outbox', 'value' => '0', 'progress' => true],
              ['label' => 'Draft Emails in Outbox', 'value' => '0'],
              ['label' => 'Sent Emails Last 30 Days', 'value' => '0'],
          ],
      ],
      [
          'title' => 'Approvals',
          'subtitle' => 'Pending Approvals',
          'tiles' => [
              ['label' => 'Requests Sent ... Approval', 'value' => '0'],
              ['label' => 'Requests to Approve', 'value' => '0'],
          ],
      ],
      [
          'title' => 'Self-Service',
          'subtitle' => 'Current Time Sheet',
          'wide' => true,
          'tiles' => [
              ['label' => '', 'value' => '', 'circle' => true, 'tone' => 'pale'],
              ['label' => 'Open Time Sheets', 'value' => '0'],
              ['label' => 'Submitted Time Sheets', 'value' => '0'],
              ['label' => 'Rejected Time Sheets', 'value' => '0'],
              ['label' => 'Approved Time Sheets', 'value' => '0'],
          ],
      ],
  ];

  $actions = [
      'Sales Quote',
      'Sales Invoice',
      'Sales Order',
      'Sales Return Order',
      'Sales Credit Memo',
      'Tasks',
      'Sales',
      'Reports',
      'History',
  ];
@endphp

@section('content')
  <div class="overflow-hidden rounded-b-3xl bg-white shadow-sm">
    <div class="px-6 py-5 lg:px-24">
      <div class="flex flex-col gap-4 lg:flex-row lg:items-center">
        <div class="text-xl font-semibold text-neutral-800">PT. Putra Muba Coal</div>
        <span class="hidden h-8 w-px bg-neutral-300 lg:block"></span>
        <nav class="flex flex-wrap items-center gap-7 text-sm text-slate-600">
          <a href="#" class="hover:text-teal-700">Sales <i class="fas fa-chevron-down ml-1 text-[10px]"></i></a>
          <a href="#" class="hover:text-teal-700">Purchasing <i class="fas fa-chevron-down ml-1 text-[10px]"></i></a>
          <a href="#" class="hover:text-teal-700">Inventory <i class="fas fa-chevron-down ml-1 text-[10px]"></i></a>
          <a href="#" class="hover:text-teal-700">Posted Documents <i class="fas fa-chevron-down ml-1 text-[10px]"></i></a>
          <span class="hidden h-8 w-px bg-neutral-300 lg:block"></span>
          <i class="fas fa-bars text-slate-500"></i>
        </nav>
      </div>

      <div class="mt-5 flex flex-wrap gap-7 text-sm font-medium text-teal-700">
        <a href="#">Items</a>
        <a href="#">Customers</a>
        <a href="#">Disposisi</a>
      </div>

      <div class="mt-9 grid gap-8 lg:grid-cols-[1.2fr_1fr]">
        <div>
          <div class="text-sm font-semibold text-slate-500">Headline</div>
          <h1 class="mt-3 max-w-2xl text-5xl font-light leading-tight text-neutral-800">
            Good afternoon, Meuthia Novianthree Nafasya!
          </h1>
          <div class="mt-7 flex gap-2">
            <span class="h-2.5 w-2.5 rounded-full bg-slate-500"></span>
            <span class="h-2.5 w-2.5 rounded-full border border-slate-400"></span>
          </div>
        </div>

        <div class="pt-1">
          <div class="text-sm font-semibold text-slate-500">Actions</div>
          <div class="mt-3 grid grid-cols-1 gap-x-8 gap-y-3 text-sm font-medium text-teal-700 sm:grid-cols-3">
            @foreach ($actions as $index => $action)
              <a href="#" class="flex items-center gap-2">
                <span class="text-lg leading-none">{!! $index < 5 ? '+' : '&rsaquo;' !!}</span>
                {{ $action }}
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <section class="mt-8">
        <h2 class="border-b-2 border-slate-500 pb-2 text-lg font-semibold text-neutral-800">
          Activities <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </h2>

        <div class="mt-4 grid gap-8 xl:grid-cols-[240px_470px_240px]">
          @foreach ($activities as $group)
            <div>
              <h3 class="mb-2 text-sm font-semibold text-neutral-800">{{ $group['title'] }}</h3>
              <div class="grid grid-cols-2 gap-2 {{ count($group['tiles']) > 2 ? 'sm:grid-cols-4' : '' }}">
                @foreach ($group['tiles'] as $tile)
                  @include('home.partials.metric-tile', ['tile' => $tile])
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </section>

      <section class="mt-12 grid gap-10 xl:grid-cols-[180px_370px_260px]">
        @foreach (array_slice($sections, 0, 3) as $section)
          <div class="{{ $section['wide'] ?? false ? 'xl:col-span-1' : '' }}">
            <h2 class="border-b border-slate-500 pb-2 text-lg font-semibold text-neutral-800">{{ $section['title'] }}</h2>
            <h3 class="mt-3 text-sm font-semibold text-neutral-800">{{ $section['subtitle'] }}</h3>
            <div class="mt-2 flex flex-wrap gap-2">
              @foreach ($section['tiles'] as $tile)
                @include('home.partials.metric-tile', ['tile' => $tile])
              @endforeach
            </div>
          </div>
        @endforeach
      </section>

      <section class="mt-12 max-w-3xl">
        @php($section = $sections[3])
        <h2 class="border-b border-slate-500 pb-2 text-lg font-semibold text-neutral-800">{{ $section['title'] }}</h2>
        <div class="mt-3 grid gap-2 sm:grid-cols-5">
          @foreach ($section['tiles'] as $tile)
            <div>
              @if ($loop->first)
                <h3 class="mb-2 text-sm font-semibold text-neutral-800">{{ $section['subtitle'] }}</h3>
              @elseif ($loop->iteration === 2)
                <h3 class="mb-2 text-sm font-semibold text-neutral-800">Time Sheets</h3>
              @elseif ($loop->iteration === 3)
                <h3 class="mb-2 text-sm font-semibold text-neutral-800">Pending Time Sheets</h3>
              @else
                <div class="mb-2 h-5"></div>
              @endif
              @include('home.partials.metric-tile', ['tile' => $tile])
            </div>
          @endforeach
        </div>
      </section>
    </div>
  </div>
@endsection
