@php
  $tone = $tile['tone'] ?? 'teal';
  $tileClasses = match ($tone) {
      'muted' => 'bg-slate-200 text-slate-600',
      'pale' => 'bg-teal-50 text-teal-700',
      default => 'bg-teal-700 text-white',
  };
@endphp

<a href="#" class="{{ $tileClasses }} block h-[122px] w-[108px] px-3 py-2 shadow-sm transition hover:brightness-95">
  @if (! empty($tile['circle']))
    <div class="flex h-full items-center justify-center">
      <span class="h-12 w-12 rounded-full border-4 border-teal-700"></span>
    </div>
  @else
    <div class="min-h-10 text-sm leading-tight">{{ $tile['label'] }}</div>
    <div class="mt-1 text-5xl font-light leading-none">{{ $tile['value'] }}</div>
    <div class="mt-3 h-px bg-current opacity-60"></div>
    @if (! empty($tile['progress']))
      <div class="mt-[-3px] h-1 w-full bg-lime-400"></div>
    @endif
    <div class="mt-2 text-lg leading-none opacity-80">&rsaquo;</div>
  @endif
</a>
