{{-- Rodapé LGPD: largura total da área útil (main). --}}
@php
    $lgpd = config('visitaai_municipio.lgpd', []);
@endphp
<div class="mt-6 w-full sm:mt-8">
    <details class="w-full max-w-none rounded-lg border border-slate-200/80 bg-white/70 px-3 py-2 text-slate-700 shadow-sm dark:border-slate-600 dark:bg-slate-900/40 dark:text-slate-300 sm:px-4">
        <summary class="cursor-pointer list-inside list-none text-left text-[11px] font-medium leading-snug text-slate-700 marker:hidden dark:text-slate-300 [&::-webkit-details-marker]:hidden">
            <span class="border-b border-dotted border-slate-400/80 pb-px dark:border-slate-500">{{ __('Proteção de dados pessoais (LGPD). Ver resumo') }}</span>
        </summary>
        <div class="mt-2 max-h-52 space-y-2 overflow-y-auto border-t border-slate-200/90 pt-2 text-left text-[10px] leading-relaxed dark:border-slate-600">
            <p class="font-medium text-slate-800 dark:text-slate-200">{{ __('Base federal') }}: {{ __('CF/1988; Lei 8.080/1990; LGPD; LAI; Marco Civil; ANPD.') }}</p>
            <p>{{ $lgpd['resumo_sistema'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span>. {{ $lgpd['encarregado_texto'] ?? '' }}</p>
            <p>{{ $lgpd['titulares_direitos'] ?? '' }}</p>
            <p class="text-slate-500 dark:text-slate-400">{{ $lgpd['atualizacao'] ?? '' }}</p>
        </div>
    </details>
</div>
