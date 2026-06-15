<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Halo Sherly Sayang</title>
  <link rel="icon" href="{{ asset('assets/img/mms.png') }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-x-hidden bg-[#fff1f6] text-[#5f2438]">

  <main class="relative flex min-h-screen items-center justify-center px-5 py-8">
    <div class="absolute inset-0 bg-[linear-gradient(135deg,#fff8fb_0%,#ffe5ef_48%,#ffc7dc_100%)]"></div>
    <div class="absolute inset-0 opacity-45 [background-image:linear-gradient(90deg,rgba(214,51,108,0.08)_1px,transparent_1px),linear-gradient(rgba(214,51,108,0.08)_1px,transparent_1px)] [background-size:44px_44px]"></div>

    <section class="relative grid w-full max-w-6xl items-center gap-8 lg:grid-cols-[1.05fr_0.95fr]">
      <div class="hidden lg:block">
        <div class="relative min-h-[620px] overflow-hidden rounded-lg border border-white/80 bg-white/35 shadow-2xl shadow-pink-200/70 backdrop-blur">
          <img src="{{ asset('assets/img/backround.png') }}" alt="Portal romantis untuk Sherly"
            class="absolute inset-0 h-full w-full object-cover opacity-25">
          <div class="absolute inset-0 bg-gradient-to-br from-[#fff7fb]/95 via-[#ffd6e6]/80 to-[#f58ab5]/60"></div>

          <div class="relative flex h-full min-h-[620px] flex-col justify-between p-10">
            <div class="flex items-center gap-3">
              <div class="flex h-12 w-12 items-center justify-center rounded-lg bg-white text-2xl text-[#d6336c] shadow-lg shadow-pink-200">
                &hearts;
              </div>
              <div>
                <p class="text-sm font-semibold uppercase tracking-[0.28em] text-[#b83262]">Portal Sayang</p>
                <p class="text-sm text-[#8b3a55]">Dibuat khusus dari Taufik</p>
              </div>
            </div>

            <div class="max-w-xl">
              <p class="mb-4 inline-flex rounded-lg bg-white/75 px-4 py-2 text-sm font-semibold text-[#c2255c] shadow-sm">
                Surprise kecil buat kamu
              </p>
              <h1 class="text-5xl font-black leading-tight text-[#7a2945]">
                Halo Sherly Sayang
              </h1>
              <p class="mt-6 text-xl leading-9 text-[#7d3b52]">
                Semangat buat laporan magangnya ya sayang. Pelan-pelan, satu halaman demi satu halaman,
                Taufik yakin Sherly bisa menyelesaikannya dengan cantik.
              </p>
            </div>

            <div class="grid grid-cols-3 gap-3 text-center">
              <div class="rounded-lg bg-white/70 p-4 shadow-sm">
                <div class="text-2xl font-black text-[#d6336c]">&hearts;</div>
                <p class="mt-1 text-xs font-semibold text-[#8b3a55]">Ditemani</p>
              </div>
              <div class="rounded-lg bg-white/70 p-4 shadow-sm">
                <div class="text-2xl font-black text-[#d6336c]">100%</div>
                <p class="mt-1 text-xs font-semibold text-[#8b3a55]">Semangat</p>
              </div>
              <div class="rounded-lg bg-white/70 p-4 shadow-sm">
                <div class="text-2xl font-black text-[#d6336c]">ILY</div>
                <p class="mt-1 text-xs font-semibold text-[#8b3a55]">From Taufik</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="mx-auto w-full max-w-md">
        <div class="rounded-lg border border-white/80 bg-white/80 p-6 shadow-2xl shadow-pink-200/80 backdrop-blur md:p-8">
          <div class="text-center">
            <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center rounded-lg bg-gradient-to-br from-[#ff8fbd] to-[#d6336c] text-4xl text-white shadow-xl shadow-pink-300/70">
              &hearts;
            </div>
            <p class="text-sm font-bold uppercase tracking-[0.28em] text-[#d6336c]">For Sherly</p>
            <h2 class="mt-3 text-3xl font-black leading-tight text-[#6f243f]">
              Halo Sherly Sayang
            </h2>
            <p class="mx-auto mt-4 max-w-sm text-sm leading-7 text-[#8a4058]">
              Hari ini portalnya dibuat manis dulu, biar Sherly masuknya tinggal klik dan langsung lanjut
              berjuang buat laporan magang.
            </p>
          </div>

          @if (session('statusLogin'))
            <div class="mt-6 rounded-lg bg-[#fff3bf] p-4 text-sm font-semibold text-[#8a5a00]">
              {{ session('statusLogin') }}
            </div>
          @elseif(session('statusLogout'))
            <div class="mt-6 rounded-lg bg-[#d3f9d8] p-4 text-sm font-semibold text-[#2b8a3e]">
              {{ session('statusLogout') }}
            </div>
          @elseif(session('error'))
            <div class="mt-6 rounded-lg bg-[#ffe3e3] p-4 text-sm font-semibold text-[#c92a2a]">
              {{ session('error') }}
            </div>
          @elseif(session('success'))
            <div class="mt-6 rounded-lg bg-[#d3f9d8] p-4 text-sm font-semibold text-[#2b8a3e]">
              {{ session('success') }}
            </div>
          @endif

          <div class="my-7 rounded-lg bg-[#fff5f9] p-5 text-center">
            <p class="text-sm font-semibold text-[#c2255c]">Pesan kecil dari Taufik</p>
            <p class="mt-3 text-base leading-7 text-[#76334a]">
              Semangat buat laporan magangnya sayanggg. Aku bangga sama kamu. I Love You.
            </p>
          </div>

          <form action="{{ route('auth.login') }}" method="POST">
            @csrf
            <input type="hidden" name="romantic_portal" value="1">

            <button type="submit"
              class="group flex w-full items-center justify-center gap-3 rounded-lg bg-gradient-to-r from-[#ff6fa8] via-[#f74f91] to-[#d6336c] px-5 py-4 text-base font-black text-white shadow-xl shadow-pink-300/70 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl focus:outline-none focus:ring-4 focus:ring-[#ffc2d7]">
              <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-white/20 text-xl transition group-hover:bg-white/30">
                &hearts;
              </span>
              Masuk ke Portal Sherly
            </button>
          </form>

          <p class="mt-6 text-center text-xs font-semibold uppercase tracking-[0.22em] text-[#c76a8b]">
            From Taufik with love
          </p>
        </div>
      </div>
    </section>
  </main>

</body>

</html>

