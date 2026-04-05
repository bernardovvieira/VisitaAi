@props([
    'href',
    'active' => false,
])

<a href="{{ $href }}" {{ $attributes->class([
    'sidebar-nav-link flex items-center gap-3 rounded-lg border-l-2 py-2.5 pl-[calc(0.75rem-2px)] pr-2.5 text-[13px] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-400/35 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950',
    'border-blue-400 bg-white/[0.07] font-medium text-white' => $active,
    'border-transparent font-medium text-slate-300/95 hover:border-transparent hover:bg-white/[0.04] hover:text-white' => ! $active,
]) }}>
    {{ $slot }}
</a>
