{{-- Rodapé LGPD: largura total da área útil (main). --}}
@php
    $lgpd = config('visitaai_municipio.lgpd', []);
@endphp
<div class="mt-6 w-full sm:mt-8">
    <x-ui.disclosure variant="footer-lgpd">
        <x-slot name="summary">
            <span class="border-b border-dotted border-slate-400/80 pb-px dark:border-slate-500">{{ __('Proteção de dados pessoais (LGPD). Ver resumo') }}</span>
        </x-slot>
        <p class="font-medium text-slate-800 dark:text-slate-200">{{ __('Base federal') }}: {{ __('CF/1988; Lei 8.080/1990; LGPD; LAI; Marco Civil; ANPD.') }}</p>
        <p>{{ $lgpd['resumo_sistema'] ?? '' }}</p>
        <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span>. {{ $lgpd['encarregado_texto'] ?? '' }}</p>
        <p>{{ $lgpd['titulares_direitos'] ?? '' }}</p>
        <p class="text-slate-500 dark:text-slate-400">{{ $lgpd['atualizacao'] ?? '' }}</p>
    </x-ui.disclosure>
</div>
