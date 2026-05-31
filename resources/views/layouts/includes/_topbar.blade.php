<div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
  <div class="flex items-center gap-3">
    <button type="button" onclick="toggleSidebar()"
      class="border-border bg-surface text-text hover:bg-surface-muted inline-flex h-10 w-10 items-center justify-center rounded-xl border transition"
      aria-label="Toggle menu">
      <i class="fas fa-bars text-sm"></i>
    </button>

    <a href="{{ route('home') }}" class="flex items-center gap-3">
      <img src="{{ asset('assets/img/mms.png') }}" alt="MKM Logo" class="h-10 w-auto object-contain">
      <div class="hidden sm:block">
        <div class="text-text text-sm font-semibold">MKM Work Permit</div>
        <div class="text-muted text-xs">Digital Work Permit System</div>
      </div>
    </a>
  </div>

  <div class="relative">
    <button type="button" id="userMenuButton"
      class="border-border bg-surface hover:bg-surface-muted flex items-center gap-3 rounded-xl border px-2 py-1.5 transition"
      aria-expanded="false" aria-haspopup="true">
      <img
        src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : asset('assets/img/profile.png') }}"
        alt="User Avatar" class="h-10 w-10 rounded-full object-cover">
      <div class="hidden text-left sm:block">
        <div class="text-text text-sm font-medium">{{ auth()->user()->name }}</div>
        <div class="text-muted text-xs">{{ auth()->user()->email }}</div>
      </div>
      <i class="fas fa-chevron-down text-muted hidden h-4 w-4 sm:block"></i>
    </button>

    <div id="userMenu"
      class="border-border bg-surface absolute right-0 z-50 mt-2 hidden w-72 overflow-hidden rounded-2xl border shadow-xl">
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
