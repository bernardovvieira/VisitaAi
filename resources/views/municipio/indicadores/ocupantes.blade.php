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
@endphp

@section('og_title', config('app.name') . ' · ' . ($cfgInd['titulo_pagina'] ?? 'Indicadores'))
@section('og_description', $cfgInd['subtitulo'] ?? '')

@section('content')
<div class="mx-auto max-w-7xl space-y-8">
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

    <header class="space-y-3">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ $cfgInd['titulo_pagina'] ?? 'Indicadores municipais' }}</h1>
                <p class="mt-1 max-w-3xl text-gray-600 dark:text-gray-400">{{ $cfgInd['subtitulo'] ?? '' }}</p>
            </div>
            @if($canExportCsv)
                <a href="{{ route('gestor.indicadores.ocupantes.export') }}"
                   class="inline-flex shrink-0 items-center justify-center rounded-xl border border-blue-600 bg-blue-50 px-4 py-2.5 text-sm font-semibold text-blue-900 shadow-sm transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:border-blue-500 dark:bg-blue-950/50 dark:text-blue-100 dark:hover:bg-blue-900/40 dark:focus-visible:ring-offset-gray-900">
                    <x-heroicon-o-arrow-down-tray class="mr-2 h-5 w-5 shrink-0" aria-hidden="true" />
                    {{ $cfgInd['export_csv_label'] ?? __('Exportar CSV') }}
                </a>
            @else
                <span class="inline-flex shrink-0 cursor-not-allowed items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-2.5 text-sm font-medium text-gray-500 dark:border-gray-600 dark:bg-gray-800/50 dark:text-gray-400"
                      title="{{ $csvHintDisabled }}"
                      aria-disabled="true">
                    <x-heroicon-o-arrow-down-tray class="mr-2 h-5 w-5 shrink-0 opacity-60" aria-hidden="true" />
                    {{ $cfgInd['export_csv_label'] ?? __('Exportar CSV') }}
                </span>
            @endif
        </div>
        @if(! $canExportCsv && $csvHintDisabled !== '')
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $csvHintDisabled }}</p>
        @endif
        <div class="rounded-xl border border-amber-200/90 bg-amber-50 p-4 text-sm leading-relaxed text-amber-950 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100">
            <p>{{ $cfgInd['aviso'] ?? '' }}</p>
            <p class="mt-1 font-medium">{{ $cfgInd['aviso_privacidade'] ?? '' }}<span class="ms-1">({{ __('mínimo: :n registros por bairro', ['n' => $painel['minimo_aplicado']]) }})</span></p>
        </div>
    </header>

    @php $R = $painel['resumo']; @endphp
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total de ocupantes registrados') }}</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($R['total_ocupantes'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Imóveis com pelo menos um ocupante') }}</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($R['total_imoveis_com_ocupante'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800 sm:col-span-2 lg:col-span-2">
            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $cfgInd['titulo_secao_faixa_global'] ?? 'Faixa etária' }}</p>
            <dl class="mt-3 grid grid-cols-2 gap-2 text-sm sm:grid-cols-5">
                @foreach($keysFaixa as $k)
                    <div class="rounded border border-gray-100 px-2 py-2 dark:border-gray-600">
                        <dt class="text-xs text-gray-500 dark:text-gray-400">{{ $labelsFaixa[$k] ?? $k }}</dt>
                        <dd class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ number_format($R['faixas_etarias'][$k] ?? 0, 0, ',', '.') }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_bairro'] ?? 'Por bairro' }}</h2>
        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('A primeira coluna permanece visível ao deslocar a tabela horizontalmente.') }}</p>
        <div class="mt-4 overflow-x-auto rounded-lg ring-1 ring-gray-200/80 dark:ring-gray-600">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-600">
                <thead>
                    <tr class="bg-gray-50 text-left dark:bg-gray-900">
                        <th scope="col" class="sticky left-0 z-20 min-w-[11rem] whitespace-nowrap border-b border-gray-200 bg-gray-50 px-3 py-3 text-left font-semibold text-gray-700 shadow-[4px_0_12px_-4px_rgba(15,23,42,0.12)] dark:border-gray-600 dark:bg-gray-900 dark:text-gray-300 dark:shadow-[4px_0_12px_-4px_rgba(0,0,0,0.4)]">{{ __('Bairro (imóvel)') }}</th>
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
                                    <td class="border-b border-gray-100 px-3 py-2.5 text-gray-800 dark:border-gray-700 dark:text-gray-200">{{ number_format($linha['faixas'][$k] ?? 0, 0, ',', '.') }}</td>
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

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_escolaridade'] ?? 'Escolaridade' }}</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach($painel['escolaridade'] as $codigo => $qtd)
                    <li class="flex justify-between gap-2 border-b border-gray-100 pb-2 dark:border-gray-700">
                        <span class="text-gray-700 dark:text-gray-300">{{ $escLabels[$codigo] ?? $codigo }}</span>
                        <span class="font-semibold text-gray-900 dark:text-gray-100">{{ number_format($qtd, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            @if(empty($painel['escolaridade']))
                <p class="mt-2 text-sm text-gray-500">{{ __('Sem dados.') }}</p>
            @endif
        </section>
        <section class="rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_renda'] ?? 'Renda' }}</h2>
            <ul class="mt-3 space-y-2 text-sm">
                @foreach($painel['renda'] as $codigo => $qtd)
                    <li class="flex justify-between gap-2 border-b border-gray-100 pb-2 dark:border-gray-700">
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
