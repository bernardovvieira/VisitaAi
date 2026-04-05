{{--
  Bloco expansível padronizado (<details>) — variantes alinhadas ao design system.

  Uso:
  <x-ui.disclosure variant="lead">
    <x-slot name="summary"><span class="border-b border-dotted ...">{{ __('Título') }}</span></x-slot>
    …conteúdo do painel…
  </x-ui.disclosure>
--}}
@props([
    'variant' => 'lead',
])

@php
    $cfg = match ($variant) {
        'lead' => [
            'wrap' => 'text-sm text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-none font-medium text-slate-800 marker:hidden dark:text-slate-200 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-2 space-y-2 border-t border-slate-200/80 pt-2 text-xs leading-relaxed dark:border-slate-600/80',
        ],
        'lead-mt' => [
            'wrap' => 'mt-2 text-sm text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-none font-medium text-slate-700 marker:hidden dark:text-slate-300 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-2 space-y-2 border-t border-slate-200/80 pt-2 text-xs leading-relaxed dark:border-slate-600/80',
        ],
        'muted-card' => [
            'wrap' => 'v-card v-card--muted text-sm text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-none font-medium text-slate-800 marker:hidden dark:text-slate-200 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-3 space-y-2 border-t border-slate-200/80 pt-3 text-xs leading-relaxed dark:border-slate-600/80',
        ],
        'muted-card-simple' => [
            'wrap' => 'v-card v-card--muted text-sm text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-none font-medium text-slate-800 marker:hidden dark:text-slate-200 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-3 border-t border-slate-200/80 pt-3 text-xs leading-relaxed dark:border-slate-600/80',
        ],
        'amber' => [
            'wrap' => 'group rounded-lg border border-amber-200/80 bg-amber-50/90 text-left dark:border-amber-800/55 dark:bg-amber-950/25',
            'summary' => 'cursor-pointer list-none px-3 py-2 text-xs font-medium text-amber-950 marker:hidden dark:text-amber-100 [&::-webkit-details-marker]:hidden',
            'panel' => 'border-t border-amber-200/70 px-3 py-2 text-[11px] leading-relaxed text-amber-950/95 dark:border-amber-800/50 dark:text-amber-100/90',
        ],
        'amber-compact' => [
            'wrap' => 'rounded-lg border border-amber-200/80 bg-amber-50/90 text-left dark:border-amber-800/60 dark:bg-amber-950/35 dark:text-amber-100',
            'summary' => 'cursor-pointer list-none px-2.5 py-2 text-xs font-medium text-amber-950 marker:hidden dark:text-amber-100 [&::-webkit-details-marker]:hidden',
            'panel' => 'border-t border-amber-200/70 px-2.5 py-2 text-[11px] leading-relaxed text-amber-950 dark:border-amber-800/50 dark:text-amber-100/90',
        ],
        'rose' => [
            'wrap' => 'mt-2 rounded-lg border border-rose-200/70 bg-white/50 text-rose-900/95 dark:border-rose-800/50 dark:bg-rose-950/20 dark:text-rose-100/90',
            'summary' => 'cursor-pointer list-none px-3 py-2 text-xs font-medium marker:hidden text-rose-900/95 dark:text-rose-100/90 [&::-webkit-details-marker]:hidden',
            'panel' => 'border-t border-rose-200/60 px-3 py-2 text-xs leading-relaxed dark:border-rose-800/50',
        ],
        'footer-lgpd' => [
            'wrap' => 'w-full max-w-none rounded-lg border border-slate-200/80 bg-white/70 px-3 py-2 text-slate-700 shadow-sm dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300 sm:px-4',
            'summary' => 'cursor-pointer list-inside list-none text-left text-[11px] font-medium leading-snug text-slate-700 marker:hidden dark:text-slate-300 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-2 max-h-52 space-y-2 overflow-y-auto border-t border-slate-200/90 pt-2 text-left text-[10px] leading-relaxed dark:border-slate-600',
        ],
        'amber-card' => [
            'wrap' => 'v-card border-amber-200/80 bg-amber-50/90 text-sm text-amber-950 shadow-sm dark:border-amber-800/60 dark:bg-amber-950/30 dark:text-amber-100',
            'summary' => 'cursor-pointer list-none px-4 py-3 font-semibold text-amber-950 marker:hidden dark:text-amber-100 sm:px-5 [&::-webkit-details-marker]:hidden',
            'panel' => 'space-y-2 border-t border-amber-200/70 px-4 pb-4 pt-3 leading-relaxed dark:border-amber-800/50 sm:px-5',
        ],
        'csv-hint' => [
            'wrap' => 'max-w-xl text-[11px] leading-snug text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-inside list-none marker:hidden text-blue-800 dark:text-blue-300 [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-1.5',
        ],
        'amber-avisos' => [
            'wrap' => 'list-none overflow-hidden rounded-lg border border-amber-200/90 bg-amber-50/95 text-[11px] leading-snug text-amber-950 shadow-sm dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100',
            'summary' => 'cursor-pointer list-none px-2.5 py-1.5 text-xs text-amber-950 marker:hidden dark:text-amber-50 [&::-webkit-details-marker]:hidden',
            'panel' => 'space-y-1 border-t border-amber-200/80 px-2.5 pb-2 pt-1.5 dark:border-amber-800/60',
        ],
        default => [
            'wrap' => 'text-sm text-slate-600 dark:text-slate-400',
            'summary' => 'cursor-pointer list-none font-medium marker:hidden [&::-webkit-details-marker]:hidden',
            'panel' => 'mt-2 border-t border-slate-200/80 pt-2 text-xs dark:border-slate-600/80',
        ],
    };
@endphp

<details {{ $attributes->merge(['class' => $cfg['wrap']]) }}>
    <summary class="{{ $cfg['summary'] }}">
        {{ $summary }}
    </summary>
    <div class="{{ $cfg['panel'] }}">
        {{ $slot }}
    </div>
</details>
