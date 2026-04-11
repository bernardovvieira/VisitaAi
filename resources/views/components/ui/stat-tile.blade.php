{{-- KPI / métrica compacta (grelhas de indicadores). Em grelha, use h-full no cartão: rótulo com altura mínima alinhada entre colunas. --}}
@props([
    'label' => null,
    'heading' => null,
    'valueClass' => null,
    'labelTitle' => null,
])

@php
    $valueRowClass = filled($heading)
        ? ($valueClass ?? 'mt-2 text-xl font-bold tabular-nums text-slate-900 dark:text-slate-50')
        : ($valueClass ?? 'mt-0.5 text-xl font-semibold tabular-nums text-slate-900 dark:text-slate-100');
@endphp

<div {{ $attributes->class(['v-card', 'v-card--tight', 'flex', 'h-full', 'min-h-0', 'flex-col']) }}
    @if(filled($labelTitle)) title="{{ $labelTitle }}" @endif>
    @if(filled($heading))
        <div class="flex min-h-[2.75rem] items-start gap-2">
            @isset($icon)
                {{ $icon }}
            @endisset
            <h3 class="line-clamp-2 min-h-0 flex-1 text-sm font-semibold leading-snug text-slate-800 dark:text-slate-100">{{ $heading }}</h3>
        </div>
        <div class="{{ $valueRowClass }}">
            {{ $slot }}
        </div>
    @else
        <p class="line-clamp-2 min-h-[2.75rem] text-xs font-medium leading-snug text-slate-500 dark:text-slate-400">{{ $label }}</p>
        <div class="{{ $valueRowClass }}">
            {{ $slot }}
        </div>
    @endif
</div>
