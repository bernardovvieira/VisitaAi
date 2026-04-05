@props([
    'context' => null,
    'compact' => false,
    /** Lista curta de legislação + cartão mais estreito (ex.: consulta pública inicial). */
    'consultaPublicaMinimal' => false,
])

@php
    $lgpd = config('visitaai_municipio.lgpd', []);
    $ctx = $context && isset($lgpd['contextos'][$context]) ? $lgpd['contextos'][$context] : null;
@endphp

@if($compact)
<details {{ $attributes->merge(['class' => 'w-full '.($consultaPublicaMinimal ? 'max-w-xl mx-auto' : 'max-w-none').' rounded-xl border border-slate-200/90 bg-white/85 text-slate-800 shadow-sm ring-1 ring-slate-900/[0.04] dark:border-slate-600/80 dark:bg-slate-900/40 dark:text-slate-200 dark:ring-white/[0.06]']) }}>
    <summary class="{{ $consultaPublicaMinimal ? 'px-2.5 py-2 text-[11px]' : 'px-3 py-2.5 text-xs sm:text-sm' }} cursor-pointer list-inside list-none font-medium text-slate-700 marker:hidden transition-colors hover:text-slate-900 dark:text-slate-200 dark:hover:text-white [&::-webkit-details-marker]:hidden">
        <span class="border-b border-dotted border-slate-400/80 pb-px dark:border-slate-500/90">{{ __('LGPD e tratamento dos dados neste painel') }}</span>
        <span class="sr-only">{{ __('Abre o texto sobre LGPD e dados.') }}</span>
    </summary>
    <div class="{{ $consultaPublicaMinimal ? 'space-y-1.5 px-2.5 pb-2.5 pt-1.5 text-[10px] leading-snug sm:text-[11px]' : 'space-y-2 px-3 pb-3 pt-2 text-[11px] leading-relaxed sm:text-xs' }} border-t border-slate-200/90 dark:border-slate-600">
        @if(filled($ctx))
            <p class="rounded-md border border-amber-200/90 bg-amber-50/90 p-1.5 text-[10px] font-medium leading-snug text-amber-950 dark:border-amber-800/70 dark:bg-amber-950/40 dark:text-amber-100 sm:p-2 sm:text-[11px] sm:leading-relaxed">{{ $ctx }}</p>
        @endif
        @if($consultaPublicaMinimal)
            <p class="leading-snug text-slate-700 dark:text-slate-300">{{ $lgpd['resumo_sistema'] ?? '' }}</p>
        @else
            <p class="font-semibold leading-snug text-slate-900 dark:text-slate-100">{{ $lgpd['titulo'] ?? '' }}</p>
            <p class="text-slate-700 dark:text-slate-300">{{ $lgpd['resumo_sistema'] ?? '' }}</p>
        @endif
        <details class="{{ $consultaPublicaMinimal ? 'rounded-md text-[10px] sm:text-[11px]' : 'rounded-md text-[11px] sm:text-xs' }} bg-slate-100/60 leading-relaxed dark:bg-slate-800/35">
            <summary class="{{ $consultaPublicaMinimal ? 'px-2 py-1' : 'px-2 py-1.5' }} cursor-pointer select-none font-medium text-slate-700 transition-colors hover:text-slate-950 dark:text-slate-300 dark:hover:text-slate-100">
                <span class="border-b border-dotted border-slate-400/75 pb-px dark:border-slate-500/80">{{ $consultaPublicaMinimal ? __('Base legal e legislação (expandir)') : __('Legislação federal e tratamento de dados (expandir)') }}</span>
            </summary>
            @if($consultaPublicaMinimal)
                <div class="mt-1 max-h-48 space-y-1.5 overflow-y-auto border-t border-slate-200/90 px-2 pb-2 pt-1.5 dark:border-slate-600/80">
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Quadro normativo federal') }}.</span> {{ $lgpd['quadro_legislacao_federal'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Bases legais') }}.</span> {{ $lgpd['bases_legais'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Direitos dos titulares') }}.</span> {{ $lgpd['titulares_direitos'] ?? '' }}</p>
                    @if(filled($lgpd['encarregado_texto'] ?? null))
                        <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span>. {{ $lgpd['encarregado_texto'] ?? '' }}</p>
                    @endif
                </div>
            @else
                <div class="mt-2 max-h-60 space-y-2 overflow-y-auto border-t border-slate-200/90 px-2 pb-2 pt-2 dark:border-slate-600/80 sm:max-h-72 sm:mt-3 sm:pt-3">
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Quadro normativo federal') }}.</span> {{ $lgpd['quadro_legislacao_federal'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Princípios (LGPD)') }}.</span> {{ $lgpd['principios_lgpd'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Controlador') }}.</span> {{ $lgpd['controlador'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Finalidades') }}.</span> {{ $lgpd['finalidades'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Bases legais') }}.</span> {{ $lgpd['bases_legais'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Categorias de dados') }}.</span> {{ $lgpd['categorias_dados'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Dados sensíveis e medidas') }}.</span> {{ $lgpd['dados_sensiveis_medidas'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Operadores') }}.</span> {{ $lgpd['operadores'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Direitos dos titulares') }}.</span> {{ $lgpd['titulares_direitos'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span>. {{ $lgpd['encarregado_texto'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Retenção') }}.</span> {{ $lgpd['retencao'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Segurança da informação') }}.</span> {{ $lgpd['seguranca'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Compartilhamento') }}.</span> {{ $lgpd['compartilhamento'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Transferência internacional') }}.</span> {{ $lgpd['transf_internacional'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Cookies e tecnologias similares') }}.</span> {{ $lgpd['cookies_tecnologia'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Lei de Acesso à Informação (LAI)') }}.</span> {{ $lgpd['lei_acesso_informacao'] ?? '' }}</p>
                    <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Autoridade Nacional de Proteção de Dados (ANPD)') }}.</span> {{ $lgpd['autoridade_nacional'] ?? '' }}</p>
                    <p class="text-[11px] text-slate-600 dark:text-slate-400">{{ $lgpd['atualizacao'] ?? '' }}</p>
                </div>
            @endif
        </details>
    </div>
</details>
@else
<div {{ $attributes->merge(['class' => 'w-full max-w-none rounded-lg border border-slate-200 bg-slate-50/90 p-3 text-slate-800 shadow-sm dark:border-slate-600 dark:bg-slate-900/45 dark:text-slate-200 sm:p-4']) }}>
    <p class="text-xs font-semibold leading-snug text-slate-900 dark:text-slate-100 sm:text-sm">{{ $lgpd['titulo'] ?? '' }}</p>
    <p class="mt-1.5 text-[11px] leading-relaxed text-slate-700 dark:text-slate-300 sm:mt-2 sm:text-xs">{{ $lgpd['resumo_sistema'] ?? '' }}</p>
    @if(filled($ctx))
        <p class="mt-2 rounded-md border border-amber-200/90 bg-amber-50/90 p-2 text-xs font-medium leading-relaxed text-amber-950 dark:border-amber-800/70 dark:bg-amber-950/40 dark:text-amber-100">{{ $ctx }}</p>
    @endif
    <details class="mt-2 rounded-md bg-slate-100/60 text-[11px] leading-relaxed dark:bg-slate-800/35 sm:mt-3 sm:text-xs">
        <summary class="cursor-pointer select-none px-2 py-1.5 font-medium text-slate-700 transition-colors hover:text-slate-950 dark:text-slate-300 dark:hover:text-slate-100">
            <span class="border-b border-dotted border-slate-400/75 pb-px dark:border-slate-500/80">{{ __('Legislação federal e tratamento de dados (expandir)') }}</span>
        </summary>
        <div class="mt-2 max-h-60 space-y-2 overflow-y-auto border-t border-slate-200/90 px-2 pb-2 pt-2 dark:border-slate-600/80 sm:max-h-72 sm:mt-3 sm:pt-3">
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Quadro normativo federal') }}.</span> {{ $lgpd['quadro_legislacao_federal'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Princípios (LGPD)') }}.</span> {{ $lgpd['principios_lgpd'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Controlador') }}.</span> {{ $lgpd['controlador'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Finalidades') }}.</span> {{ $lgpd['finalidades'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Bases legais') }}.</span> {{ $lgpd['bases_legais'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Categorias de dados') }}.</span> {{ $lgpd['categorias_dados'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Dados sensíveis e medidas') }}.</span> {{ $lgpd['dados_sensiveis_medidas'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Operadores') }}.</span> {{ $lgpd['operadores'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ __('Direitos dos titulares') }}.</span> {{ $lgpd['titulares_direitos'] ?? '' }}</p>
            <p><span class="font-semibold text-slate-900 dark:text-slate-100">{{ $lgpd['encarregado_titulo'] ?? '' }}</span>. {{ $lgpd['encarregado_texto'] ?? '' }}</p>
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
@endif
