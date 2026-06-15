@php
  $isFormsActive = request()->is('sales-contracts*') || request()->is('dropdown*') || request()->is('rule*') || request()->is('user*');
@endphp

<div class="relative">
  <div class="flex h-11 items-center justify-between bg-neutral-900 px-4 text-white">
    <div class="flex min-w-0 items-center gap-5">
      <a href="{{ route('home') }}" class="truncate text-sm font-semibold">
        Dynamics 365 Business Central
      </a>

      <nav class="hidden items-center gap-5 text-sm text-white/80 lg:flex" aria-label="Primary navigation">
        <a href="{{ route('home') }}" class="{{ request()->is('home') ? 'text-white' : 'hover:text-white' }}">
          Dashboard
        </a>
        <a href="{{ route('calendar.index') }}" class="{{ request()->is('calendar*') ? 'text-white' : 'hover:text-white' }}">
          Calendar
        </a>
        <a href="{{ route('sales-contracts.create') }}" class="{{ $isFormsActive ? 'text-white' : 'hover:text-white' }}">
          Forms
        </a>
      </nav>
    </div>

    <div class="flex items-center gap-5 text-sm text-white/80">
      <button type="button" id="mobileTopbarMenuButton" onclick="toggleTopbarMenu()"
        class="inline-flex h-8 w-8 items-center justify-center rounded hover:bg-white/10 lg:hidden"
        aria-label="Toggle menu">
        <i class="fas fa-bars"></i>
      </button>

      <i class="fas fa-search hidden sm:block"></i>
      <i class="far fa-bell hidden sm:block"></i>
      <i class="fas fa-gear hidden sm:block"></i>
      <i class="fas fa-question hidden sm:block"></i>

      <div class="relative">
        <button type="button" id="userMenuButton"
          class="flex h-8 w-8 items-center justify-center rounded-full bg-red-800 text-sm font-semibold text-white"
          aria-expanded="false" aria-haspopup="true">
          I
        </button>

        <div id="userMenu"
          class="border-border bg-surface absolute right-0 z-50 mt-2 hidden w-72 overflow-hidden rounded-2xl border text-slate-900 shadow-xl">
          <div class="border-border border-b px-4 py-4">
            <div class="flex items-center gap-3">
              <img
                src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/profile.png') }}"
                alt="User Avatar" class="h-12 w-12 rounded-full object-cover">
              <div class="min-w-0">
                <div class="text-text truncate text-sm font-semibold">{{ auth()->user()->name }}</div>
                <div class="text-muted truncate text-xs">{{ auth()->user()->email }}</div>
              </div>
            </div>
          </div>

          <div class="p-2">
            <button type="button" onclick="openChangePasswordModal(); closeUserMenu();"
              class="text-text hover:bg-surface-muted flex w-full items-center gap-3 rounded-xl px-3 py-3 text-left text-sm font-medium transition">
              <i class="fas fa-key w-4 text-center"></i>
              Change Password
            </button>

            <a href="{{ url('/logout') }}"
              class="text-text hover:bg-surface-muted flex items-center gap-3 rounded-xl px-3 py-3 text-sm font-medium transition">
              <i class="fas fa-right-from-bracket w-4 text-center"></i>
              Logout
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <nav id="mobileTopbarMenu"
    class="absolute inset-x-0 top-11 z-40 hidden border-t border-white/10 bg-neutral-900 px-4 py-3 shadow-lg lg:hidden"
    aria-label="Mobile navigation">
    <div class="flex flex-col gap-1 text-sm text-white/80">
      <a href="{{ route('home') }}" onclick="closeTopbarMenu()"
        class="{{ request()->is('home') ? 'bg-white/10 text-white' : 'hover:bg-white/10 hover:text-white' }} rounded px-3 py-2 transition">
        Dashboard
      </a>
      <a href="{{ route('calendar.index') }}" onclick="closeTopbarMenu()"
        class="{{ request()->is('calendar*') ? 'bg-white/10 text-white' : 'hover:bg-white/10 hover:text-white' }} rounded px-3 py-2 transition">
        Calendar
      </a>
      <a href="{{ route('sales-contracts.create') }}" onclick="closeTopbarMenu()"
        class="{{ $isFormsActive ? 'bg-white/10 text-white' : 'hover:bg-white/10 hover:text-white' }} rounded px-3 py-2 transition">
        Forms
      </a>
    </div>
  </nav>
</div>
