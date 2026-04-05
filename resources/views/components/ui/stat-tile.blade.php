{{-- KPI / métrica compacta (grelhas de indicadores). --}}
@props([
    'label' => null,
    'heading' => null,
    'valueClass' => null,
])

@php
    $valueRowClass = filled($heading)
        ? ($valueClass ?? 'mt-2 text-xl font-bold tabular-nums text-slate-900 dark:text-slate-50')
        : ($valueClass ?? 'mt-0.5 text-xl font-semibold tabular-nums text-slate-900 dark:text-slate-100');
@endphp

<div {{ $attributes->class(['v-card', 'v-card--tight']) }}>
    @if(filled($heading))
        <div class="flex items-center gap-2">
            @isset($icon)
                {{ $icon }}
            @endisset
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100">{{ $heading }}</h3>
        </div>
        <div class="{{ $valueRowClass }}">
            {{ $slot }}
        </div>
    @else
        <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $label }}</p>
        <div class="{{ $valueRowClass }}">
            {{ $slot }}
        </div>
    @endif
</div>
