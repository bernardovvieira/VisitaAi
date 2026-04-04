@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-t-lg border-b-2 border-blue-600 px-2 pt-1 text-sm font-semibold leading-5 text-slate-900 transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:border-blue-400 dark:text-slate-100 nav-link-active'
            : 'inline-flex items-center border-b-2 border-transparent px-2 pt-1 text-sm font-medium leading-5 text-slate-500 transition hover:border-slate-300 hover:text-slate-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/30 dark:text-slate-400 dark:hover:border-slate-600 dark:hover:text-slate-200';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
