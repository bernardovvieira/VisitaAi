@props([
    'label' => '',
    'first' => false,
])
@if(!$first)
    <div class="sidebar-section-divider mx-2 my-4 h-px bg-white/[0.08] lg:my-3" role="presentation" aria-hidden="true"></div>
@endif
<div class="sidebar-section-label px-3 pb-1.5 pt-0.5">
    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
</div>
