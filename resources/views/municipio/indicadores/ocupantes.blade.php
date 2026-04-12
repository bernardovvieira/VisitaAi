@extends('layouts.app')

@php
    $cfgInd = config('visitaai_municipio.indicadores', []);
    $labelsFaixa = $cfgInd['colunas_faixas'] ?? [];
    $keysFaixa = ['0-11', '12-17', '18-59', '60+', 'sem_info'];
    $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
    $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
    $corLabels = config('visitaai_municipio.cor_raca_opcoes', []);
    $trabLabels = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $sexoLabels = config('visitaai_socioeconomico.sexo_opcoes', []);
    $ecLabels = config('visitaai_socioeconomico.estado_civil_opcoes', []);
    $parLabels = config('visitaai_socioeconomico.parentesco_opcoes', []);
    $rfiLabels = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
    $celSup = $cfgInd['texto_celula_suprimida'] ?? '-';
    $faixasCounts = [];
    foreach ($keysFaixa as $_k) {
        $faixasCounts[$_k] = (int) (($painel['resumo']['faixas_etarias'][$_k] ?? 0));
    }
    $maxFaixaCount = max($faixasCounts) ?: 1;
    $totalFaixaGeral = max(1, array_sum($faixasCounts));
    $maxIdadePorColuna = [];
    foreach ($keysFaixa as $_k) {
        $maxIdadePorColuna[$_k] = 0;
        foreach ($painel['por_bairro'] ?? [] as $_linha) {
            if (empty($_linha['suprimido']) && is_array($_linha['faixas'] ?? null)) {
                $maxIdadePorColuna[$_k] = max($maxIdadePorColuna[$_k], (int) ($_linha['faixas'][$_k] ?? 0));
            }
        }
        $maxIdadePorColuna[$_k] = max(1, $maxIdadePorColuna[$_k]);
    }
    $ariaFaixaCalor = collect($keysFaixa)->map(function ($k) use ($labelsFaixa, $faixasCounts) {
        return ($labelsFaixa[$k] ?? $k).': '.$faixasCounts[$k];
    })->implode('; ');
    $__ogIndicadores = trim(implode(' ', array_filter([
        (string) ($cfgInd['subtitulo'] ?? ''),
        (string) ($cfgInd['subtitulo_detalhe'] ?? ''),
    ])));
    $Q = $painel['completude'];
    $R = $painel['resumo'];
    $completudeBarras = [
        ['label' => __('Data de nascimento'), 'pct' => 'pct_data_nascimento', 'com' => 'com_data_nascimento', 'bar' => 'bg-emerald-600 dark:bg-emerald-500'],
        ['label' => __('Sexo'), 'pct' => 'pct_sexo_informado', 'com' => 'com_sexo_informado', 'bar' => 'bg-cyan-600 dark:bg-cyan-500'],
        ['label' => __('Estado civil'), 'pct' => 'pct_estado_civil_informado', 'com' => 'com_estado_civil_informado', 'bar' => 'bg-sky-600 dark:bg-sky-500'],
        ['label' => __('Parentesco com o titular'), 'pct' => 'pct_parentesco_informado', 'com' => 'com_parentesco_informado', 'bar' => 'bg-violet-600 dark:bg-violet-500'],
        ['label' => __('Escolaridade'), 'pct' => 'pct_escolaridade_informada', 'com' => 'com_escolaridade_informada', 'bar' => 'bg-blue-600 dark:bg-blue-500'],
        ['label' => __('Renda (faixa)'), 'pct' => 'pct_renda_informada', 'com' => 'com_renda_informada', 'bar' => 'bg-indigo-600 dark:bg-indigo-500'],
        ['label' => __('Renda formal / informal'), 'pct' => 'pct_renda_formal_informal_informada', 'com' => 'com_renda_formal_informal_informada', 'bar' => 'bg-fuchsia-600 dark:bg-fuchsia-500'],
        ['label' => __('Cor/raça'), 'pct' => 'pct_cor_raca_informada', 'com' => 'com_cor_raca_informada', 'bar' => 'bg-teal-600 dark:bg-teal-500'],
        ['label' => __('Trabalho'), 'pct' => 'pct_situacao_trabalho_informada', 'com' => 'com_situacao_trabalho_informada', 'bar' => 'bg-amber-600 dark:bg-amber-500'],
    ];
@endphp

@section('og_title', config('app.name') . ' · ' . ($cfgInd['titulo_pagina'] ?? __('Indicadores')))
@section('og_description', filled($__ogIndicadores) ? $__ogIndicadores : __('Painel agregado de ocupantes no Visita Aí: bairro do imóvel, faixas etárias, perfil sociodemográfico e economia declarada, com critérios de privacidade e uso institucional.'))

@section('content')
<div class="v-page v-page--wide v-page--loose">
    <x-breadcrumbs :items="[
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => $cfgInd['titulo_pagina'] ?? __('Indicadores')],
    ]" />

    <x-flash-alerts />

    <x-page-header :eyebrow="__('Gestão municipal')" :title="$cfgInd['titulo_pagina'] ?? __('Indicadores')">
        <x-slot:lead>
            @if(filled($cfgInd['subtitulo'] ?? ''))
                <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">{{ $cfgInd['subtitulo'] }}</p>
            @endif
        </x-slot:lead>
    </x-page-header>

    <div class="flex flex-col items-end gap-2 sm:flex-row sm:flex-wrap sm:items-center sm:justify-end">
        <a href="{{ route('gestor.indicadores.ocupantes.export') }}"
              class="v-btn-export v-btn-export--sheet no-underline">
            <x-heroicon-o-arrow-down-tray class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ $cfgInd['botao_export_csv'] ?? __('Exportar CSV') }}
        </a>
    </div>

    {{-- Visão geral --}}
    <section class="space-y-3" aria-labelledby="ind-sec-visao">
        <h2 id="ind-sec-visao" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
            {{ __('Visão geral') }}
        </h2>
        <div class="grid grid-cols-1 items-stretch gap-2 sm:grid-cols-3">
            <x-ui.stat-tile class="h-full shadow-md shadow-slate-200/25 dark:shadow-none" :label="__('Total de ocupantes')">
                {{ number_format($R['total_ocupantes'], 0, ',', '.') }}
            </x-ui.stat-tile>
            <x-ui.stat-tile class="h-full shadow-md shadow-slate-200/25 dark:shadow-none" :label="__('Imóveis com ocupante')">
                {{ number_format($R['total_imoveis_com_ocupante'], 0, ',', '.') }}
            </x-ui.stat-tile>
            <x-ui.stat-tile
                class="h-full shadow-md shadow-slate-200/25 dark:shadow-none"
                :label="__('Titular ou ref. familiar')"
                :label-title="__('Titular ou ref. familiar (descrição)')">
                {{ number_format($R['ocupantes_referencia_familiar'] ?? 0, 0, ',', '.') }}
            </x-ui.stat-tile>
        </div>
        <div class="v-card v-card--tight shadow-md shadow-slate-200/25 dark:shadow-none">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">{{ $cfgInd['titulo_secao_faixa_global'] ?? __('Faixa etária') }}</p>
                <p class="text-[10px] font-medium uppercase tracking-wide text-blue-600/90 dark:text-blue-400/90">{{ __('Mapa de calor') }}</p>
            </div>
            <div class="mt-3 flex overflow-hidden rounded-xl ring-1 ring-slate-200/90 dark:ring-slate-600"
                 role="img"
                 aria-label="{{ __('Distribuição por faixa etária') }}: {{ $ariaFaixaCalor }}">
                @foreach($keysFaixa as $k)
                    @php
                        $cFaixa = $faixasCounts[$k] ?? 0;
                        $relFaixa = $maxFaixaCount > 0 ? $cFaixa / $maxFaixaCount : 0;
                        $pctFaixaTotal = $totalFaixaGeral > 0 ? (int) round(100 * $cFaixa / $totalFaixaGeral) : 0;
                        $heatOp = $cFaixa > 0 ? 0.1 + 0.78 * $relFaixa : 0.07;
                        $faixaTxtLight = $relFaixa >= 0.5;
                    @endphp
                    <div class="relative flex min-h-[5.25rem] min-w-0 flex-1 flex-col items-center justify-center px-1 py-2.5 text-center sm:px-1.5">
                        <div class="absolute inset-0 bg-slate-100 dark:bg-slate-900/85" aria-hidden="true"></div>
                        <div class="absolute inset-0 bg-gradient-to-b from-sky-200 via-blue-500 to-blue-900 dark:from-sky-500 dark:via-blue-600 dark:to-blue-950"
                             style="opacity: {{ $heatOp }}"
                             aria-hidden="true"></div>
                        <span class="relative z-10 max-w-full text-[10px] font-semibold leading-tight {{ $faixaTxtLight ? 'text-white drop-shadow-sm' : 'text-slate-700 dark:text-slate-200' }}">{{ $labelsFaixa[$k] ?? $k }}</span>
                        <span class="relative z-10 mt-1 text-base font-bold tabular-nums sm:text-lg {{ $faixaTxtLight ? 'text-white drop-shadow' : 'text-slate-900 dark:text-slate-50' }}">{{ number_format($cFaixa, 0, ',', '.') }}</span>
                        <span class="relative z-10 mt-0.5 text-[10px] font-semibold tabular-nums {{ $faixaTxtLight ? 'text-white/90' : 'text-slate-500 dark:text-slate-400' }}">{{ $pctFaixaTotal }}%</span>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Completude --}}
    <x-section-card class="v-card--tight shadow-md shadow-slate-200/20 dark:shadow-none">
        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-200">{{ $cfgInd['titulo_secao_completude'] ?? __('Qualidade do preenchimento') }}</h2>
        @if(filled($cfgInd['subtitulo_completude'] ?? ''))
            <details class="mt-1 text-xs text-slate-600 dark:text-slate-400">
                <summary class="cursor-pointer list-inside list-none font-medium text-slate-600 marker:hidden dark:text-slate-400 [&::-webkit-details-marker]:hidden">
                    {{ __('Notas sobre completude') }}
                </summary>
                <p class="mt-1.5 leading-relaxed text-slate-500 dark:text-slate-400">{{ $cfgInd['subtitulo_completude'] }}</p>
            </details>
        @endif
        <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($completudeBarras as $row)
                @php
                    $pctVal = (int) ($Q[$row['pct']] ?? 0);
                    $comVal = (int) ($Q[$row['com']] ?? 0);
                @endphp
                <div>
                    <div class="flex items-baseline justify-between gap-2">
                        <p class="text-xs font-medium text-slate-600 dark:text-slate-300">{{ $row['label'] }}</p>
                        <p class="text-sm font-semibold tabular-nums text-slate-900 dark:text-slate-100">{{ $pctVal }}%</p>
                    </div>
                    <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-slate-200 dark:bg-slate-700">
                        <div class="h-full rounded-full transition-[width] {{ $row['bar'] }}" style="width: {{ min(100, $pctVal) }}%"></div>
                    </div>
                    <p class="mt-1.5 text-[10px] text-slate-500 dark:text-slate-400">
                        {{ number_format($comVal, 0, ',', '.') }} {{ __('de') }} {{ number_format($Q['total'], 0, ',', '.') }} {{ trans_choice('ocupante|ocupantes', $Q['total']) }}
                    </p>
                </div>
            @endforeach
        </div>
    </x-section-card>

    @php
        $mostrarLegendaTabela = filled($cfgInd['legenda_mapa_calor_tabela'] ?? '') && ($painel['resumo']['total_ocupantes'] ?? 0) > 0;
    @endphp
    <details class="text-[11px] leading-snug text-slate-600 dark:text-slate-400">
        <summary class="cursor-pointer list-inside list-none font-medium text-slate-700 marker:hidden dark:text-slate-300 [&::-webkit-details-marker]:hidden">
            {{ __('Como ler mapas de calor e tabela por bairro') }}
        </summary>
        <div class="mt-2 space-y-1.5 border-t border-slate-200/80 pt-2 dark:border-slate-600">
            <p>{{ __('Deslize a tabela para ver todas as colunas; a coluna do bairro fica fixa.') }}</p>
            <p class="text-slate-500 dark:text-slate-400">{{ __('Mínimo de :n registros por bairro para exibir totais.', ['n' => $painel['minimo_aplicado']]) }}</p>
            <p class="text-slate-500 dark:text-slate-400">{{ __('Mínimo de :n ocupantes por célula no cruzamento escolaridade × renda para exibir o número (evita identificação).', ['n' => $painel['cruzamento_escolaridade_renda']['minimo_celula_aplicado'] ?? 5]) }}</p>
            @if(filled($cfgInd['legenda_mapa_calor_faixa'] ?? ''))
                <p>{{ $cfgInd['legenda_mapa_calor_faixa'] }}</p>
            @endif
            @if($mostrarLegendaTabela)
                <p class="text-blue-700/90 dark:text-blue-300/85">{{ $cfgInd['legenda_mapa_calor_tabela'] }}</p>
            @endif
        </div>
    </details>

    {{-- Geografia --}}
    <section class="space-y-3" aria-labelledby="ind-sec-geo">
        <h2 id="ind-sec-geo" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
            {{ __('Território e idade por bairro') }}
        </h2>
        <x-section-card class="v-card--tight shadow-md shadow-slate-200/20 dark:shadow-none">
            <h3 class="text-base font-semibold text-slate-800 dark:text-slate-200">{{ $cfgInd['titulo_secao_bairro'] ?? __('Por bairro') }}</h3>
            <div class="mt-3 overflow-x-auto rounded-lg ring-1 ring-slate-200/80 dark:ring-slate-600">
                <table class="min-w-full border-collapse text-[13px] leading-snug text-slate-800 dark:text-slate-100">
                    <thead>
                        <tr class="bg-slate-50 text-left dark:bg-slate-900/95">
                            <th scope="col" class="sticky left-0 z-20 min-w-[11rem] border-b border-slate-200/90 bg-slate-50 px-3 py-2.5 text-left align-bottom text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 shadow-[4px_0_12px_-4px_rgba(15,23,42,0.12)] dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400 dark:shadow-[4px_0_12px_-4px_rgba(0,0,0,0.4)]">
                                <span class="block leading-tight normal-case tracking-normal">{{ __('Bairro') }}</span>
                                <span class="mt-0.5 block text-[10px] font-normal normal-case tracking-normal text-slate-500 dark:text-slate-400">{{ __('do imóvel') }}</span>
                            </th>
                            <th scope="col" class="whitespace-nowrap border-b border-slate-200/90 bg-slate-50 px-3 py-2.5 text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400">{{ __('Total') }}</th>
                            @foreach($keysFaixa as $k)
                                <th scope="col" class="whitespace-nowrap border-b border-slate-200/90 bg-slate-50 px-3 py-2.5 text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400">{{ $labelsFaixa[$k] ?? $k }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @forelse($painel['por_bairro'] as $linha)
                            <tr class="group v-table-row-interactive-sticky">
                                <th scope="row" class="sticky left-0 z-10 max-w-xs truncate border-b border-gray-100 bg-white px-3 py-2.5 text-left font-medium text-gray-900 shadow-[4px_0_12px_-6px_rgba(15,23,42,0.15)] group-hover:bg-blue-50/45 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:shadow-[4px_0_12px_-6px_rgba(0,0,0,0.45)] dark:group-hover:bg-slate-800/65" title="{{ $linha['bairro'] }}">{{ $linha['bairro'] }}</th>
                                @if($linha['suprimido'])
                                    <td class="border-b border-gray-100 px-3 py-2.5 text-gray-400 dark:border-gray-700" colspan="{{ 1 + count($keysFaixa) }}" title="{{ __('Agregado suprimido para este recorte.') }}">{{ $celSup }}</td>
                                @else
                                    <td class="border-b border-gray-100 px-3 py-2.5 text-gray-900 dark:border-gray-700 dark:text-gray-100">{{ number_format($linha['total'] ?? 0, 0, ',', '.') }}</td>
                                    @foreach($keysFaixa as $k)
                                        @php
                                            $vIdade = (int) ($linha['faixas'][$k] ?? 0);
                                            $maxCol = $maxIdadePorColuna[$k] ?? 1;
                                            $heatCol = $maxCol > 0 ? $vIdade / $maxCol : 0;
                                            $heatAlpha = 0.05 + 0.28 * $heatCol;
                                        @endphp
                                        <td class="relative border-b border-gray-100 px-3 py-2.5 dark:border-gray-700">
                                            <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-sky-300 to-blue-800 dark:from-blue-500 dark:to-blue-950" style="opacity: {{ $heatAlpha }}" aria-hidden="true"></span>
                                            <span class="relative z-10 tabular-nums text-gray-900 dark:text-gray-100">{{ number_format($vIdade, 0, ',', '.') }}</span>
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @empty
                            <tr><td class="px-3 py-8 text-center text-gray-500 dark:text-gray-400" colspan="{{ 2 + count($keysFaixa) }}">{{ __('Nenhum ocupante registrado ainda.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-section-card>
    </section>

    {{-- Distribuições --}}
    <section class="space-y-3" aria-labelledby="ind-sec-dist">
        <h2 id="ind-sec-dist" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
            {{ __('Distribuições agregadas') }}
        </h2>
        <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => $cfgInd['titulo_secao_escolaridade'] ?? __('Escolaridade'),
                'itens' => $painel['escolaridade'],
                'labels' => $escLabels,
                'totalBase' => $R['total_ocupantes'],
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => $cfgInd['titulo_secao_renda'] ?? __('Renda'),
                'itens' => $painel['renda'],
                'labels' => $rendaLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $escLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => __('Sexo'),
                'itens' => $painel['sexo'] ?? [],
                'labels' => $sexoLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $sexoLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => __('Estado civil'),
                'itens' => $painel['estado_civil'] ?? [],
                'labels' => $ecLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $ecLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => __('Parentesco com o titular'),
                'itens' => $painel['parentesco'] ?? [],
                'labels' => $parLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $parLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => __('Renda formal / informal'),
                'itens' => $painel['renda_formal_informal'] ?? [],
                'labels' => $rfiLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $rfiLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => $cfgInd['titulo_secao_cor_raca'] ?? __('Cor ou raça'),
                'itens' => $painel['cor_raca'] ?? [],
                'labels' => $corLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $corLabels['nao_informado'] ?? __('Não informado'),
            ])
            @include('municipio.indicadores._distribuicao_lista', [
                'titulo' => $cfgInd['titulo_secao_situacao_trabalho'] ?? __('Situação no trabalho'),
                'itens' => $painel['situacao_trabalho'] ?? [],
                'labels' => $trabLabels,
                'totalBase' => $R['total_ocupantes'],
                'naoInformadoLabel' => $trabLabels['nao_informado'] ?? __('Não informado'),
            ])
        </div>
    </section>

    @php
        $cruz = $painel['cruzamento_escolaridade_renda'];
        $keysCruzEsc = array_keys($cruz['linhas']);
        $keysCruzRenda = array_keys($cruz['colunas']);
    @endphp
    @if(($cruz['total_cruzamento'] ?? 0) > 0)
        <section class="space-y-3" aria-labelledby="ind-sec-cruz">
            <h2 id="ind-sec-cruz" class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 dark:text-slate-400">
                {{ __('Cruzamentos') }}
            </h2>
            <x-section-card class="v-card--tight shadow-md shadow-slate-200/20 dark:shadow-none">
                <h3 class="text-base font-semibold text-slate-800 dark:text-slate-200">{{ $cfgInd['titulo_secao_cruzamento'] ?? __('Escolaridade e renda') }}</h3>
                <details class="mt-1 text-[10px] leading-snug text-slate-500 dark:text-slate-400">
                    <summary class="cursor-pointer list-inside list-none font-medium text-slate-600 marker:hidden dark:text-slate-400 [&::-webkit-details-marker]:hidden">
                        {{ __('Notas sobre o cruzamento') }}
                    </summary>
                    <div class="mt-1.5 space-y-1.5">
                        @if(filled($cfgInd['legenda_cruzamento'] ?? ''))
                            <p>{{ $cfgInd['legenda_cruzamento'] }}</p>
                        @endif
                        <p>{{ __('Base') }}: {{ number_format($cruz['total_cruzamento'], 0, ',', '.') }} {{ trans_choice('ocupante|ocupantes', $cruz['total_cruzamento']) }}.</p>
                    </div>
                </details>
                <div class="mt-3 overflow-x-auto rounded-lg ring-1 ring-slate-200/80 dark:ring-slate-600">
                    <table class="min-w-full border-collapse text-[13px] leading-snug text-slate-800 dark:text-slate-100">
                        <thead>
                            <tr class="bg-slate-50 text-left dark:bg-slate-900/95">
                                <th scope="col" class="sticky left-0 z-10 min-w-[10rem] border-b border-slate-200/90 bg-slate-50 px-2 py-2.5 text-left text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400">{{ $cfgInd['titulo_secao_escolaridade'] ?? __('Escolaridade') }}</th>
                                @foreach($keysCruzRenda as $kr)
                                    <th scope="col" class="whitespace-nowrap border-b border-slate-200/90 bg-slate-50 px-2 py-2.5 text-center text-[10px] font-semibold uppercase tracking-[0.06em] text-slate-500 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-400">{{ $cruz['colunas'][$kr] ?? $kr }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($keysCruzEsc as $ke)
                                <tr>
                                    <th scope="row" class="sticky left-0 z-10 border-b border-gray-100 bg-white px-2 py-2 text-left font-medium text-gray-900 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100">{{ $cruz['linhas'][$ke] ?? $ke }}</th>
                                    @foreach($keysCruzRenda as $kr)
                                        @php $cell = $cruz['celulas'][$ke][$kr] ?? ['count' => 0, 'suprimido' => false, 'pct_total' => 0]; @endphp
                                        <td class="border-b border-gray-100 px-2 py-2 text-center tabular-nums dark:border-gray-700">
                                            @if(!empty($cell['suprimido']))
                                                <span class="text-gray-400 dark:text-gray-500" title="{{ __('Célula suprimida (poucos casos).') }}">{{ $celSup }}</span>
                                            @else
                                                <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format((int) ($cell['count'] ?? 0), 0, ',', '.') }}</span>
                                                @if(($cell['count'] ?? 0) > 0 && ($cell['pct_total'] ?? null) !== null)
                                                    <span class="block text-[10px] font-normal text-slate-500 dark:text-slate-400">{{ $cell['pct_total'] }}%</span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-section-card>
        </section>
    @endif
</div>
@endsection
