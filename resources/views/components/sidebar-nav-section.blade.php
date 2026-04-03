@props([
    'label' => '',
    'first' => false,
])
@if(!$first)
    <div class="mx-2 my-3 h-px bg-slate-800/90" role="presentation" aria-hidden="true"></div>
@endif
<div class="px-3 pb-1 pt-1">
    <p class="text-[10px] font-bold uppercase tracking-[0.12em] text-slate-500">{{ $label }}</p>
</div>
