<nav class="flex h-full min-h-0 w-full flex-col text-white">
  <div class="min-h-0 flex-1 overflow-y-auto p-3">

    {{-- Section title --}}
    <div class="mb-3 px-3">
      <div class="text-sidebar-text-muted text-xs font-semibold uppercase tracking-[0.2em]">
        Configuration
      </div>
    </div>

    @if (auth()->user()->role === 'IT')
      @php
        $masterConfigOpen = request()->is('dropdown*') || request()->is('rule*') || request()->is('user*');
      @endphp

      <div x-data="{ open: {{ $masterConfigOpen ? 'true' : 'false' }} }" class="space-y-1">

        {{-- Parent menu --}}
        <button type="button" @click="open = !open"
          class="text-sidebar-text-soft hover:bg-sidebar-hover flex w-full items-center justify-between rounded-xl px-3 py-3 text-left text-sm font-medium transition">
          <span class="flex items-center gap-3">
            <i class="fas fa-screwdriver-wrench w-4 text-center"></i>
            Master Configuration
          </span>

          <i class="fas fa-chevron-down transition-transform duration-300" :class="{ 'rotate-180': open }"></i>
        </button>

        {{-- Child menu --}}
        <div x-show="open" x-collapse class="space-y-1 pl-4">

          <a href="{{ url('/dropdown') }}"
            class="{{ request()->is('dropdown*')
                ? 'bg-sidebar-hover text-sidebar-text font-semibold'
                : 'text-sidebar-text-soft hover:bg-sidebar-hover' }} block rounded-xl px-3 py-2.5 text-sm transition">
            Dropdown
          </a>

          <a href="{{ url('/rule') }}"
            class="{{ request()->is('rule*')
                ? 'bg-sidebar-hover text-sidebar-text font-semibold'
                : 'text-sidebar-text-soft hover:bg-sidebar-hover' }} block rounded-xl px-3 py-2.5 text-sm transition">
            Rules
          </a>

          <a href="{{ url('/user') }}"
            class="{{ request()->is('user*')
                ? 'bg-sidebar-hover text-sidebar-text font-semibold'
                : 'text-sidebar-text-soft hover:bg-sidebar-hover' }} block rounded-xl px-3 py-2.5 text-sm transition">
            User
          </a>

        </div>
      </div>
    @endif
  </div>

  {{-- Bottom user info --}}
  <div class="border-sidebar-border bg-sidebar-dark mt-auto border-t px-4 py-4 text-white">
    <div class="text-sidebar-text-muted text-xs">Logged in as</div>
    <div class="mt-1 truncate text-sm font-semibold">
      {{ auth()->user()->name }}
    </div>
  </div>
</nav>
