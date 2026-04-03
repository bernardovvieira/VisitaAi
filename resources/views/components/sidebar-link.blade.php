@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}" {{ $attributes->class([
    'flex items-center gap-3 rounded-r-lg border-l-[3px] py-2.5 pl-[calc(0.75rem-3px)] pr-3 text-sm transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/40 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
    'border-blue-400 bg-slate-800/95 font-semibold text-white shadow-sm' => $active,
    'border-transparent font-medium text-slate-300 hover:border-transparent hover:bg-slate-800/60 hover:text-white' => ! $active,
]) }}>
    {{ $slot }}
</a>
