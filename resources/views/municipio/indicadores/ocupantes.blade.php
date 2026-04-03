@extends('layouts.app')

@php
    $cfgInd = config('visitaai_municipio.indicadores', []);
    $labelsFaixa = $cfgInd['colunas_faixas'] ?? [];
    $keysFaixa = ['0-11', '12-17', '18-59', '60+', 'sem_info'];
    $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
    $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
    $celSup = $cfgInd['texto_celula_suprimida'] ?? '-';
    $canExportCsv = ($painel['resumo']['total_ocupantes'] ?? 0) > 0;
    $csvHintDisabled = $cfgInd['export_csv_disabled_hint'] ?? '';
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
@endphp

@section('og_title', config('app.name') . ' · ' . ($cfgInd['titulo_pagina'] ?? 'Indicadores'))
@section('og_description', trim(implode(' ', array_filter([(string) ($cfgInd['subtitulo'] ?? ''), (string) ($cfgInd['subtitulo_detalhe'] ?? '')]))))

@section('content')
<div class="mx-auto max-w-7xl space-y-4">
    <x-breadcrumbs :items="[
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => $cfgInd['titulo_pagina'] ?? 'Indicadores'],
    ]" />

    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" :autodismiss="false" />
    @endif
    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif

    <header class="space-y-2">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
            <div class="min-w-0 flex-1">
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $cfgInd['titulo_pagina'] ?? __('Indicadores') }}</h1>
                <p class="mt-1 max-w-3xl text-gray-600 dark:text-gray-400">{{ $cfgInd['subtitulo'] ?? '' }}</p>
                @if(filled($cfgInd['subtitulo_detalhe'] ?? ''))
                    <p class="mt-1 max-w-3xl text-xs leading-relaxed text-slate-500/90 dark:text-slate-400/85">{{ $cfgInd['subtitulo_detalhe'] }}</p>
                @endif
            </div>
            <div class="flex shrink-0 flex-col items-stretch gap-1 sm:items-end">
                @if($canExportCsv)
                    <a href="{{ route('gestor.indicadores.ocupantes.export') }}"
                       class="inline-flex items-center justify-center rounded-lg border border-emerald-600 bg-emerald-50 px-3 py-2 text-xs font-semibold text-emerald-950 shadow-sm transition hover:bg-emerald-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 focus-visible:ring-offset-2 dark:border-emerald-500 dark:bg-emerald-950/45 dark:text-emerald-100 dark:hover:bg-emerald-900/50 dark:focus-visible:ring-offset-gray-900">
                        <x-heroicon-o-arrow-down-tray class="mr-1.5 h-4 w-4 shrink-0" aria-hidden="true" />
                        {{ $cfgInd['export_csv_label'] ?? __('Exportar CSV') }}
                    </a>
                    @if(filled($cfgInd['export_csv_legenda'] ?? ''))
                        <p class="max-w-[16rem] text-right text-[10px] leading-snug text-slate-500/80 dark:text-slate-400/75">{{ $cfgInd['export_csv_legenda'] }}</p>
                    @endif
                @else
                    <span class="inline-flex cursor-not-allowed items-center justify-center rounded-lg border border-dashed border-emerald-300/80 bg-emerald-50/50 px-3 py-2 text-xs font-medium text-emerald-800/70 dark:border-emerald-800 dark:bg-emerald-950/20 dark:text-emerald-200/50"
                          title="{{ $csvHintDisabled }}"
                          aria-disabled="true">
                        <x-heroicon-o-arrow-down-tray class="mr-1.5 h-4 w-4 shrink-0 opacity-50" aria-hidden="true" />
                        {{ $cfgInd['export_csv_label'] ?? __('Exportar CSV') }}
                    </span>
                    @if(filled($cfgInd['export_csv_legenda'] ?? ''))
                        <p class="max-w-[16rem] text-right text-[10px] leading-snug text-slate-500/70 dark:text-slate-400/60">{{ $cfgInd['export_csv_legenda'] }}</p>
                    @endif
                @endif
            </div>
        </div>
        @if(! $canExportCsv && $csvHintDisabled !== '')
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $csvHintDisabled }}</p>
        @endif
        <div class="rounded-xl border border-amber-200/90 bg-amber-50/95 p-3 text-xs leading-relaxed text-amber-950 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100">
            <p><span class="font-semibold text-amber-900 dark:text-amber-50">{{ __('Atenção') }}:</span> {{ $cfgInd['aviso'] ?? '' }}</p>
            <p class="mt-1.5 text-amber-900/95 dark:text-amber-100/95">{{ $cfgInd['aviso_privacidade'] ?? '' }}</p>
            <p class="mt-1.5 text-[10px] leading-snug text-amber-800/70 dark:text-amber-200/65">{{ __('Mínimo de :n registros por bairro para exibir totais.', ['n' => $painel['minimo_aplicado']]) }}</p>
        </div>
    </header>

    @php $R = $painel['resumo']; @endphp
    <section class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/25 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total de ocupantes') }}</p>
            <p class="mt-0.5 text-2xl font-semibold tabular-nums text-slate-900 dark:text-slate-100">{{ number_format($R['total_ocupantes'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/25 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5">
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Imóveis com ocupante') }}</p>
            <p class="mt-0.5 text-2xl font-semibold tabular-nums text-slate-900 dark:text-slate-100">{{ number_format($R['total_imoveis_com_ocupante'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/25 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5 sm:col-span-2 lg:col-span-2">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">{{ $cfgInd['titulo_secao_faixa_global'] ?? 'Faixa etária' }}</p>
                <p class="text-[10px] font-medium uppercase tracking-wide text-orange-600/90 dark:text-orange-400/90">{{ __('Mapa de calor') }}</p>
            </div>
            <div class="mt-3 flex gap-1 overflow-hidden rounded-xl ring-1 ring-slate-200/90 dark:ring-slate-600 sm:gap-1.5"
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
                        <div class="absolute inset-0 rounded-lg bg-slate-100 dark:bg-slate-900/85" aria-hidden="true"></div>
                        <div class="absolute inset-0 rounded-lg bg-gradient-to-b from-amber-300 via-orange-500 to-rose-600 dark:from-amber-400 dark:via-orange-500 dark:to-rose-600"
                             style="opacity: {{ $heatOp }}"
                             aria-hidden="true"></div>
                        <span class="relative z-10 max-w-full text-[10px] font-semibold leading-tight {{ $faixaTxtLight ? 'text-white drop-shadow-sm' : 'text-slate-700 dark:text-slate-200' }}">{{ $labelsFaixa[$k] ?? $k }}</span>
                        <span class="relative z-10 mt-1 text-base font-bold tabular-nums sm:text-lg {{ $faixaTxtLight ? 'text-white drop-shadow' : 'text-slate-900 dark:text-slate-50' }}">{{ number_format($cFaixa, 0, ',', '.') }}</span>
                        <span class="relative z-10 mt-0.5 text-[10px] font-semibold tabular-nums {{ $faixaTxtLight ? 'text-white/90' : 'text-slate-500 dark:text-slate-400' }}">{{ $pctFaixaTotal }}%</span>
                    </div>
                @endforeach
            </div>
            @if(filled($cfgInd['legenda_mapa_calor_faixa'] ?? ''))
                <p class="mt-2 text-[10px] leading-snug text-slate-500 dark:text-slate-400">{{ $cfgInd['legenda_mapa_calor_faixa'] }}</p>
            @endif
        </div>
    </section>

    <section class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/20 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5">
        <h2 class="text-base font-semibold text-slate-800 dark:text-slate-200">{{ $cfgInd['titulo_secao_bairro'] ?? 'Por bairro' }}</h2>
        <p class="mt-0.5 text-[11px] text-slate-500 dark:text-slate-400">{{ __('Deslize a tabela para ver todas as colunas; a coluna do bairro fica fixa.') }}</p>
        @if(filled($cfgInd['legenda_mapa_calor_tabela'] ?? '') && ($painel['resumo']['total_ocupantes'] ?? 0) > 0)
            <p class="mt-1 text-[10px] leading-snug text-orange-700/85 dark:text-orange-300/80">{{ $cfgInd['legenda_mapa_calor_tabela'] }}</p>
        @endif
        <div class="mt-3 overflow-x-auto rounded-lg ring-1 ring-slate-200/80 dark:ring-slate-600">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-600">
                <thead>
                    <tr class="bg-gray-50 text-left dark:bg-gray-900">
                        <th scope="col" class="sticky left-0 z-20 min-w-[11rem] border-b border-gray-200 bg-gray-50 px-3 py-2.5 text-left align-bottom font-semibold text-gray-700 shadow-[4px_0_12px_-4px_rgba(15,23,42,0.12)] dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:shadow-[4px_0_12px_-4px_rgba(0,0,0,0.4)]">
                            <span class="block leading-tight">{{ __('Bairro') }}</span>
                            <span class="mt-0.5 block text-[10px] font-normal text-slate-500 dark:text-slate-400">{{ __('do imóvel') }}</span>
                        </th>
                        <th scope="col" class="whitespace-nowrap border-b border-gray-200 bg-gray-50 px-3 py-3 font-semibold text-gray-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ __('Total') }}</th>
                        @foreach($keysFaixa as $k)
                            <th scope="col" class="whitespace-nowrap border-b border-gray-200 bg-gray-50 px-3 py-3 font-semibold text-gray-700 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300">{{ $labelsFaixa[$k] ?? $k }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($painel['por_bairro'] as $linha)
                        <tr class="group bg-white transition-colors hover:bg-gray-50/80 dark:bg-gray-800 dark:hover:bg-gray-800/80">
                            <th scope="row" class="sticky left-0 z-10 max-w-xs truncate border-b border-gray-100 bg-white px-3 py-2.5 text-left font-medium text-gray-900 shadow-[4px_0_12px_-6px_rgba(15,23,42,0.15)] group-hover:bg-gray-50/80 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:shadow-[4px_0_12px_-6px_rgba(0,0,0,0.45)] dark:group-hover:bg-gray-800/80" title="{{ $linha['bairro'] }}">{{ $linha['bairro'] }}</th>
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
                                        <span class="pointer-events-none absolute inset-0 bg-gradient-to-br from-amber-400 to-rose-600 dark:from-amber-500 dark:to-rose-600" style="opacity: {{ $heatAlpha }}" aria-hidden="true"></span>
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
    </section>

    <div class="grid grid-cols-1 gap-3 lg:grid-cols-2">
        <section class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/20 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_escolaridade'] ?? 'Escolaridade' }}</h2>
            <ul class="mt-2 space-y-1.5 text-sm">
                @foreach($painel['escolaridade'] as $codigo => $qtd)
                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-1.5 dark:border-slate-700">
                        <span class="text-gray-700 dark:text-gray-300">{{ $escLabels[$codigo] ?? $codigo }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($qtd, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            @if(empty($painel['escolaridade']))
                <p class="mt-2 text-sm text-gray-500">{{ __('Sem dados.') }}</p>
            @endif
        </section>
        <section class="rounded-xl border border-slate-200/80 bg-white p-3 shadow-md shadow-slate-200/20 ring-1 ring-slate-100/80 dark:border-slate-600 dark:bg-slate-800/90 dark:shadow-none dark:ring-white/5">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_renda'] ?? 'Renda' }}</h2>
            <ul class="mt-2 space-y-1.5 text-sm">
                @foreach($painel['renda'] as $codigo => $qtd)
                    <li class="flex justify-between gap-2 border-b border-slate-100 pb-1.5 dark:border-slate-700">
                        <span class="text-gray-700 dark:text-gray-300">{{ $rendaLabels[$codigo] ?? ($codigo === 'nao_informado' ? ($escLabels['nao_informado'] ?? 'Não informado') : $codigo) }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($qtd, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            @if(empty($painel['renda']))
                <p class="mt-2 text-sm text-gray-500">{{ __('Sem dados.') }}</p>
            @endif
        </section>
    </div>
</div>
@endsection
