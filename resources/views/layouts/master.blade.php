<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="description" content="" />
  <meta name="author" content="" />
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>mnc Project</title>

  <link rel="icon" href="{{ asset('assets/img/mms.png') }}">
  <meta name="theme-color" content="rgba(0, 103, 127, 1)" />
  <link rel="apple-touch-icon" href="{{ asset('logo.png') }}">
  <link rel="manifest" href="{{ asset('/manifest.json') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-page text-text h-full antialiased">
  <div class="flex h-screen flex-col overflow-hidden">
    <header class="sticky top-0 z-40">
      @include('layouts.includes._topbar')
    </header>

    <main id="mainContent" class="min-h-0 flex flex-1 flex-col overflow-y-auto">
      @if (session('password'))
        <script>
          window.addEventListener('DOMContentLoaded', function() {
            alert(@json(session('password')));
          });
        </script>
      @endif

      <div class="mx-auto flex-1 w-full max-w-screen-2xl px-4 py-4 sm:px-6 sm:py-6 lg:px-8">
        @yield('content')
      </div>

      <footer class="border-border bg-surface mt-auto border-t">
        <div
          class="text-muted mx-auto flex w-full max-w-screen-2xl flex-col gap-2 px-4 py-4 text-sm sm:px-6 lg:flex-row lg:items-center lg:justify-between lg:px-8">
          <div></div>
          <div class="text-left lg:text-right">
            Copyright mnc Project &copy; {{ now()->year }}
          </div>
        </div>
      </footer>
    </main>
  </div>
  <div id="changePasswordModal" class="fixed inset-0 z-[60] hidden items-center justify-center bg-black/50 px-4">
    <div class="border-border bg-surface w-full max-w-md rounded-2xl border shadow-2xl">
      <div class="border-border flex items-center justify-between border-b px-5 py-4">
        <h2 class="text-text text-lg font-semibold">Change Password</h2>
        <button type="button" data-close-modal
          class="border-border text-text hover:bg-surface-muted inline-flex h-10 w-10 items-center justify-center rounded-lg border transition">
          x
        </button>
      </div>

      <form method="POST" action="{{ route('changePassword') }}">
        @csrf

        <div class="space-y-4 px-5 py-5">
          <div>
            <label for="oldPassword" class="text-text-soft mb-2 block text-sm font-medium">Old Password</label>
            <input type="password" id="oldPassword" name="old_password" required
              class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
          </div>

          <div>
            <label for="newPassword" class="text-text-soft mb-2 block text-sm font-medium">New Password</label>
            <input type="password" id="newPassword" name="new_password" required
              class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
          </div>

          <div>
            <label for="confirmPassword" class="text-text-soft mb-2 block text-sm font-medium">Confirm New
              Password</label>
            <input type="password" id="confirmPassword" name="new_password_confirmation" required
              class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border bg-white px-4 py-3 text-sm outline-none transition focus:ring-2">
          </div>
        </div>

        <div class="border-border flex items-center justify-end gap-3 border-t px-5 py-4">
          <button type="button" data-close-modal
            class="border-border text-text hover:bg-surface-muted rounded-xl border px-4 py-2.5 text-sm font-medium transition">
            Close
          </button>

          <button type="submit"
            class="bg-primary hover:bg-primary-dark rounded-xl px-4 py-2.5 text-sm font-semibold text-white transition">
            Change Password
          </button>
        </div>
      </form>
    </div>
  </div>

  <div id="loader" class="fixed inset-0 z-[70] hidden items-center justify-center bg-white/70 backdrop-blur-sm"
    aria-live="polite" aria-busy="true">
    <div class="flex flex-col items-center gap-3">
      <div class="border-border border-t-primary h-12 w-12 animate-spin rounded-full border-4"></div>
      <span class="text-text-soft text-sm font-medium">Loading...</span>
    </div>
  </div>

  <script>
    let Toast = null;
    let Confirm = null;

    function formatDate(dateString) {
      const date = new Date(dateString);

      return date.toLocaleDateString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
      });
    }

    async function confirmAction(options = {}) {
      if (!Confirm) return false;

      const result = await Confirm.fire({
        title: options.title ?? 'Are you sure?',
        text: options.text ?? '',
      });

      return result.isConfirmed;
    }

    async function handleDelete(event, ruleName) {
      event.preventDefault();

      const confirmed = await confirmAction({
        title: 'Delete Rule?',
        text: `Rule "${ruleName}" will be permanently deleted.`
      });

      if (confirmed) {
        event.target.submit();
      }

      return false;
    }

    async function inputPrompt({
      title = 'Input',
      text = '',
      input = 'text',
      inputPlaceholder = '',
      confirmButtonText = 'Submit',
      cancelButtonText = 'Cancel'
    } = {}) {
      if (!window.Swal) return null;

      const {
        value,
        isConfirmed
      } = await window.Swal.fire({
        title,
        text,
        input,
        inputPlaceholder,
        showCancelButton: true,
        confirmButtonText,
        cancelButtonText,
        reverseButtons: true,
        focusCancel: true,
        inputValidator: (value) => {
          if (!value || !value.trim()) {
            return 'Remark is required';
          }
        }
      });

      if (!isConfirmed) {
        return null;
      }

      return value.trim();
    }

    document.addEventListener('DOMContentLoaded', function() {
      let timeoutAlert;
      const timeoutWarning = 3300000;

      const loader = document.getElementById('loader');
      const mobileTopbarMenu = document.getElementById('mobileTopbarMenu');
      const mobileTopbarMenuButton = document.getElementById('mobileTopbarMenuButton');
      const changePasswordModal = document.getElementById('changePasswordModal');
      const closeButtons = document.querySelectorAll('[data-close-modal]');
      const userMenuButton = document.getElementById('userMenuButton');
      const userMenu = document.getElementById('userMenu');

      function showLoader() {
        loader?.classList.remove('hidden');
        loader?.classList.add('flex');
      }

      function hideLoader() {
        loader?.classList.add('hidden');
        loader?.classList.remove('flex');
      }

      function resetActivityTimer() {
        clearTimeout(timeoutAlert);
        timeoutAlert = setTimeout(function() {
          if (!window.Swal) return;

          window.Swal.fire({
            title: 'Session Timeout',
            text: 'Your session is about to expire due to inactivity. Please refresh the page to continue.',
            icon: 'warning',
            confirmButtonText: 'Refresh Page',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false,
            showCancelButton: false
          }).then((result) => {
            if (result.isConfirmed) {
              window.location.reload();
            }
          });
        }, timeoutWarning);
      }

      if (window.Swal) {
        Toast = window.Swal.mixin({
          toast: true,
          position: 'top-end',
          showConfirmButton: false,
          timer: 3500,
          timerProgressBar: true,
          didOpen: (toast) => {
            toast.addEventListener('mouseenter', window.Swal.stopTimer);
            toast.addEventListener('mouseleave', window.Swal.resumeTimer);
          }
        });

        Confirm = window.Swal.mixin({
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'Cancel',
          reverseButtons: true,
          focusCancel: true
        });

        window.Toast = Toast;
        window.Confirm = Confirm;
      }

      window.toggleTopbarMenu = function() {
        mobileTopbarMenu?.classList.toggle('hidden');
      };

      window.closeTopbarMenu = function() {
        mobileTopbarMenu?.classList.add('hidden');
      };

      window.openChangePasswordModal = function() {
        changePasswordModal?.classList.remove('hidden');
        changePasswordModal?.classList.add('flex');
      };

      window.closeChangePasswordModal = function() {
        changePasswordModal?.classList.add('hidden');
        changePasswordModal?.classList.remove('flex');
      };

      closeButtons.forEach((btn) => {
        btn.addEventListener('click', window.closeChangePasswordModal);
      });

      changePasswordModal?.addEventListener('click', function(e) {
        if (e.target === changePasswordModal) {
          window.closeChangePasswordModal();
        }
      });

      window.closeUserMenu = function() {
        userMenu?.classList.add('hidden');
        userMenuButton?.setAttribute('aria-expanded', 'false');
      };

      function openUserMenu() {
        userMenu?.classList.remove('hidden');
        userMenuButton?.setAttribute('aria-expanded', 'true');
      }

      function toggleUserMenu() {
        if (!userMenu) return;
        const isHidden = userMenu.classList.contains('hidden');
        if (isHidden) {
          openUserMenu();
        } else {
          closeUserMenu();
        }
      }

      userMenuButton?.addEventListener('click', function(e) {
        e.stopPropagation();
        toggleUserMenu();
      });

      document.addEventListener('click', function(e) {
        if (userMenu && userMenuButton && !userMenu.contains(e.target) && !userMenuButton.contains(e.target)) {
          closeUserMenu();
        }

        if (
          mobileTopbarMenu &&
          mobileTopbarMenuButton &&
          !mobileTopbarMenu.contains(e.target) &&
          !mobileTopbarMenuButton.contains(e.target)
        ) {
          closeTopbarMenu();
        }
      });

      hideLoader();

      let isExporting = false;
      document.querySelectorAll('.export-excel').forEach((button) => {
        button.addEventListener('click', function() {
          isExporting = true;
          showLoader();
        });
      });

      window.addEventListener('beforeunload', function() {
        if (!isExporting) {
          showLoader();
        }
      });

      window.addEventListener('pageshow', function(event) {
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
          hideLoader();
        }
      });

      window.addEventListener('focus', function() {
        hideLoader();
        isExporting = false;
      });

      window.onload = resetActivityTimer;
      document.onmousemove = resetActivityTimer;
      document.onkeypress = resetActivityTimer;
      document.onclick = resetActivityTimer;
      document.onscroll = resetActivityTimer;
    });
  </script>

  @stack('scripts')

  @if (session('success'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Toast) {
          window.Toast.fire({
            icon: 'success',
            title: @json(session('success'))
          });
        }
      });
    </script>
  @endif

  @if (session('error'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Toast) {
          window.Toast.fire({
            icon: 'error',
            title: @json(session('error'))
          });
        }
      });
    </script>
  @endif

  @if (session('warning'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Toast) {
          window.Toast.fire({
            icon: 'warning',
            title: @json(session('warning'))
          });
        }
      });
    </script>
  @endif

  @if (session('info'))
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Toast) {
          window.Toast.fire({
            icon: 'info',
            title: @json(session('info'))
          });
        }
      });
    </script>
  @endif

  @if ($errors->any())
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        if (window.Toast) {
          window.Toast.fire({
            icon: 'error',
            title: 'Validation error',
            text: {!! json_encode(implode("\n", $errors->all())) !!}
          });
        }
      });
    </script>
  @endif
</body>

</html>

