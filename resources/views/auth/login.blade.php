<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="icon" href="{{ asset('assets/img/mms.png') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-page text-text">

  <div class="flex min-h-screen flex-col md:flex-row">

    <div class="bg-surface-muted hidden items-center justify-center p-6 md:flex md:w-1/2">
      <div class="relative aspect-[4/3] w-full max-w-4xl overflow-hidden rounded-2xl shadow-lg">
        <img src="{{ asset('assets/img/backround.png') }}" alt="Background" class="h-full w-full object-contain">

        <div class="absolute inset-0 flex items-center justify-center">
          <div class="rounded-2xl bg-black/35 px-8 py-6 text-center text-white backdrop-blur-sm">
            <h1 class="text-3xl font-bold">MKM Base App</h1>
            <p class="mt-2 text-sm opacity-90">Base Application</p>
          </div>
        </div>
      </div>
    </div>

    <div class="flex flex-1 items-center justify-center p-6">
      <div class="bg-surface w-full max-w-md rounded-2xl p-6 shadow-xl">

        <div class="mb-6 text-center">
          <img src="{{ asset('assets/img/mms.png') }}" class="mx-auto mb-3 h-12" alt="Logo">
          <h2 class="text-xl font-bold">MKM Base App</h2>
          <p class="text-muted text-sm">Base Application</p>
        </div>

        @if (session('statusLogin'))
          <div class="mb-4 rounded-lg bg-yellow-100 p-3 text-sm text-yellow-800">
            {{ session('statusLogin') }}
          </div>
        @elseif(session('statusLogout'))
          <div class="mb-4 rounded-lg bg-green-100 p-3 text-sm text-green-800">
            {{ session('statusLogout') }}
          </div>
        @endif

        <form action="{{ url('auth/login') }}" method="POST" class="space-y-4">
          @csrf

          <input type="text" name="email" value="{{ old('email') }}" placeholder="Email or Username"
            class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border px-4 py-3 focus:outline-none focus:ring-2">

          <input type="password" name="password" placeholder="Password"
            class="border-border focus:border-primary focus:ring-primary-soft w-full rounded-xl border px-4 py-3 focus:outline-none focus:ring-2">

          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
              <input type="checkbox" name="remember" class="border-border rounded">
              Remember me
            </label>

            <a href="#" class="text-primary hover:underline">
              Forgot password?
            </a>
          </div>

          <button type="submit"
            class="bg-primary hover:bg-primary-dark w-full rounded-xl py-3 font-semibold text-white transition">
            Log In
          </button>
        </form>

        <div class="my-4 flex items-center gap-3">
          <div class="bg-border h-px flex-1"></div>
          <span class="text-muted text-xs">OR</span>
          <div class="bg-border h-px flex-1"></div>
        </div>

        <a href="#"
          class="border-border hover:bg-surface-muted flex w-full items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-medium">
          <i class="fab fa-windows"></i>
          Continue with MKM Account
        </a>

        <div class="text-muted mt-6 text-center text-xs">
          © {{ now()->year }} PT Mitsubishi Krama Yudha Motors and Manufacturing
        </div>
      </div>
    </div>

  </div>

</body>

</html>
