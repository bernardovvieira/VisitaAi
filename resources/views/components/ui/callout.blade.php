{{--
  Faixa informativa não expansível (pendências, avisos de lista).

  <x-ui.callout variant="amber" :title="__('Título')">…</x-ui.callout>
--}}
@props([
    'variant' => 'amber',
    'title' => null,
])

@php
    $wrap = match ($variant) {
        'amber' => 'v-card border-amber-200/70 bg-amber-50/90 dark:border-amber-800/60 dark:bg-amber-950/30',
        'slate' => 'v-card v-card--muted',
        'rose' => 'v-card border-rose-200/80 bg-rose-50/80 dark:border-rose-900/80 dark:bg-rose-950/40',
        default => 'v-card',
    };
    $titleClass = match ($variant) {
        'amber' => 'text-sm font-semibold text-amber-950 dark:text-amber-100',
        'rose' => 'text-sm font-semibold text-rose-950 dark:text-rose-100',
        default => 'v-section-title',
    };
@endphp

<div {{ $attributes->merge(['class' => $wrap]) }}>
    @if(filled($title))
        <h2 class="{{ $titleClass }}">{{ $title }}</h2>
    @endif
    {{ $slot }}
</div>
