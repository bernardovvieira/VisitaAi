@props([
    'context' => null,
])

@php
    $lgpd = config('visitaai_municipio.lgpd', []);
    $ctx = $context && isset($lgpd['contextos'][$context]) ? $lgpd['contextos'][$context] : null;
@endphp

<div {{ $attributes->merge(['class' => 'rounded-lg border border-slate-200 bg-slate-50/90 p-4 text-slate-800 shadow-sm dark:border-slate-600 dark:bg-slate-900/45 dark:text-slate-200']) }}>
    <p class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['titulo'] ?? '' }}</p>
    <p class="mt-2 text-xs leading-relaxed text-slate-700 dark:text-slate-300">{{ $lgpd['resumo_sistema'] ?? '' }}</p>
    @if(filled($ctx))
        <p class="mt-2 rounded-md border border-amber-200/90 bg-amber-50/90 p-2 text-xs font-medium leading-relaxed text-amber-950 dark:border-amber-800/70 dark:bg-amber-950/40 dark:text-amber-100">{{ $ctx }}</p>
    @endif
    <details class="mt-3 text-xs leading-relaxed">
        <summary class="cursor-pointer select-none font-medium text-blue-800 underline-offset-2 hover:underline dark:text-blue-300">
            {{ __('Legislação federal aplicável e tratamento de dados pessoais') }}
        </summary>
        <div class="mt-3 max-h-[24rem] space-y-2 overflow-y-auto border-t border-slate-200 pt-3 dark:border-slate-600">
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Quadro normativo federal') }}.</span> {{ $lgpd['quadro_legislacao_federal'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Princípios (LGPD)') }}.</span> {{ $lgpd['principios_lgpd'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Controlador') }}.</span> {{ $lgpd['controlador'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Finalidades') }}.</span> {{ $lgpd['finalidades'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Bases legais') }}.</span> {{ $lgpd['bases_legais'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Categorias de dados') }}.</span> {{ $lgpd['categorias_dados'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Dados sensíveis e medidas') }}.</span> {{ $lgpd['dados_sensiveis_medidas'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Operadores') }}.</span> {{ $lgpd['operadores'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Direitos dos titulares') }}.</span> {{ $lgpd['titulares_direitos'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span> — {{ $lgpd['encarregado_texto'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Retenção') }}.</span> {{ $lgpd['retencao'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Segurança da informação') }}.</span> {{ $lgpd['seguranca'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Compartilhamento') }}.</span> {{ $lgpd['compartilhamento'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Transferência internacional') }}.</span> {{ $lgpd['transf_internacional'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Cookies e tecnologias similares') }}.</span> {{ $lgpd['cookies_tecnologia'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Lei de Acesso à Informação (LAI)') }}.</span> {{ $lgpd['lei_acesso_informacao'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Autoridade Nacional de Proteção de Dados (ANPD)') }}.</span> {{ $lgpd['autoridade_nacional'] ?? '' }}</p>
            <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ $lgpd['atualizacao'] ?? '' }}</p>
        </div>
    </details>
</div>
