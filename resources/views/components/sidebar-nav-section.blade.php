@props([
    'label' => '',
    'first' => false,
    'hint' => null,
])
@if(!$first)
    <div class="sidebar-section-divider mx-2 my-4 h-px bg-white/[0.08] lg:my-3" role="presentation" aria-hidden="true"></div>
@endif
<div class="sidebar-section-label px-3 pb-1.5 pt-0.5">
    <p class="text-[10px] font-semibold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
    @if (filled($hint))
        <p class="mt-1 text-[9px] font-normal normal-case leading-snug tracking-normal text-slate-500/90">{{ $hint }}</p>
    @endif
</div>
