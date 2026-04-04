{{-- Rodapé informativo LGPD — área autenticada (não substitui política oficial do município). --}}
@php
    $lgpd = config('visitaai_municipio.lgpd', []);
@endphp
<details class="mx-auto mt-10 max-w-4xl rounded-lg border border-slate-200/90 bg-white/60 px-4 py-3 text-slate-700 shadow-sm dark:border-slate-600 dark:bg-slate-900/50 dark:text-slate-300">
    <summary class="cursor-pointer select-none text-xs font-semibold text-slate-800 dark:text-slate-200">
        {{ $lgpd['titulo'] ?? __('Proteção de dados (LGPD)') }}
    </summary>
    <div class="mt-3 space-y-2 border-t border-slate-200 pt-3 text-[11px] leading-relaxed dark:border-slate-600">
        <p class="font-medium text-slate-800 dark:text-slate-200">{{ __('Base federal') }}: {{ __('CF/1988 (saúde e intimidade); Lei nº 8.080/1990; LGPD (Lei nº 13.709/2018); LAI (Lei nº 12.527/2011); Marco Civil (Lei nº 12.965/2014); ANPD.') }}</p>
        <p>{{ $lgpd['resumo_sistema'] ?? '' }}</p>
        <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span> {{ $lgpd['encarregado_texto'] ?? '' }}</p>
        <p>{{ $lgpd['titulares_direitos'] ?? '' }}</p>
        <p class="text-slate-500 dark:text-slate-400">{{ $lgpd['atualizacao'] ?? '' }}</p>
    </div>
</details>
