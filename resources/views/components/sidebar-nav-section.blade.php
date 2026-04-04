@props([
    'label' => '',
    'first' => false,
])
@if(!$first)
    <div class="sidebar-section-divider mx-2 my-4 h-px bg-white/[0.08] lg:my-3" role="presentation" aria-hidden="true"></div>
@endif
<div class="sidebar-section-label px-3 pb-1.5 pt-0.5">
    <p class="border-l-2 border-blue-500/50 pl-2 text-[10px] font-bold uppercase tracking-[0.14em] text-slate-400">{{ $label }}</p>
</div>
