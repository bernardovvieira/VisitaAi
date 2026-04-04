@props(['active'])

@php
$classes = ($active ?? false)
            ? 'responsive-nav-link-active block w-full rounded-r-lg border-l-4 border-blue-600 bg-blue-50/90 py-2.5 ps-3 pe-4 text-start text-base font-semibold text-blue-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 dark:border-blue-400 dark:bg-blue-950/35 dark:text-blue-100'
            : 'block w-full rounded-r-lg border-l-4 border-transparent py-2.5 ps-3 pe-4 text-start text-base font-medium text-slate-600 transition hover:border-slate-300 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/25 dark:text-slate-400 dark:hover:border-slate-600 dark:hover:bg-slate-800/60 dark:hover:text-slate-100';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
