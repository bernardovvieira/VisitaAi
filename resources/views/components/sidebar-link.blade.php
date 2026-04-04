@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}" {{ $attributes->class([
    'sidebar-nav-link flex items-center gap-2.5 rounded-lg border-l-[3px] py-2 pl-[calc(0.75rem-3px)] pr-2.5 text-[13px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/40 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
    'border-blue-400 bg-blue-500/15 font-semibold text-white shadow-[inset_0_0_0_1px_rgb(96_165_250/0.25)]' => $active,
    'border-transparent font-medium text-slate-300 hover:border-transparent hover:bg-white/5 hover:text-white' => ! $active,
]) }}>
    {{ $slot }}
</a>
