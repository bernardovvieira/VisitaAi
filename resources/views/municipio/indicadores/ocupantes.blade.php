@extends('layouts.app')

@php
    $cfgInd = config('visitaai_municipio.indicadores', []);
    $labelsFaixa = $cfgInd['colunas_faixas'] ?? [];
    $keysFaixa = ['0-11', '12-17', '18-59', '60+', 'sem_info'];
    $escLabels = config('visitaai_municipio.escolaridade_opcoes', []);
    $rendaLabels = config('visitaai_municipio.renda_faixa_opcoes', []);
    $celSup = $cfgInd['texto_celula_suprimida'] ?? '—';
@endphp

@section('og_title', config('app.name') . ' — ' . ($cfgInd['titulo_pagina'] ?? 'Indicadores'))
@section('og_description', $cfgInd['subtitulo'] ?? '')

@section('content')
<div class="mx-auto max-w-7xl space-y-8">
    <x-breadcrumbs :items="[
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => $cfgInd['titulo_pagina'] ?? 'Indicadores'],
    ]" />

    <header class="space-y-2">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $cfgInd['titulo_pagina'] ?? 'Indicadores municipais' }}</h1>
        <p class="text-gray-600 dark:text-gray-400">{{ $cfgInd['subtitulo'] ?? '' }}</p>
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm text-amber-900 dark:border-amber-700 dark:bg-amber-900/30 dark:text-amber-100">
            <p>{{ $cfgInd['aviso'] ?? '' }}</p>
            <p class="mt-1 font-medium">{{ $cfgInd['aviso_privacidade'] ?? '' }}<span class="ms-1">({{ __('mínimo: :n registros por bairro', ['n' => $painel['minimo_aplicado']]) }})</span></p>
        </div>
    </header>

    @php $R = $painel['resumo']; @endphp
    <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Total de ocupantes registrados') }}</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($R['total_ocupantes'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Imóveis com pelo menos um ocupante') }}</p>
            <p class="mt-1 text-3xl font-semibold text-gray-900 dark:text-gray-100">{{ number_format($R['total_imoveis_com_ocupante'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-lg bg-white p-4 shadow dark:bg-gray-800 sm:col-span-2 lg:col-span-2">
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

    <section class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">{{ $cfgInd['titulo_secao_bairro'] ?? 'Por bairro' }}</h2>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm dark:divide-gray-600">
                <thead>
                    <tr class="bg-gray-50 text-left dark:bg-gray-900">
                        <th class="whitespace-nowrap px-3 py-2 font-semibold text-gray-700 dark:text-gray-300">{{ __('Bairro (imóvel)') }}</th>
                        <th class="whitespace-nowrap px-3 py-2 font-semibold text-gray-700 dark:text-gray-300">{{ __('Total') }}</th>
                        @foreach($keysFaixa as $k)
                            <th class="whitespace-nowrap px-3 py-2 font-semibold text-gray-700 dark:text-gray-300">{{ $labelsFaixa[$k] ?? $k }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($painel['por_bairro'] as $linha)
                        <tr class="bg-white dark:bg-gray-800">
                            <td class="max-w-xs truncate px-3 py-2 font-medium text-gray-900 dark:text-gray-100" title="{{ $linha['bairro'] }}">{{ $linha['bairro'] }}</td>
                            @if($linha['suprimido'])
                                <td class="px-3 py-2 text-gray-400" colspan="{{ 1 + count($keysFaixa) }}" title="{{ __('Agregado suprimido para este recorte.') }}">{{ $celSup }}</td>
                            @else
                                <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ number_format($linha['total'] ?? 0, 0, ',', '.') }}</td>
                                @foreach($keysFaixa as $k)
                                    <td class="px-3 py-2 text-gray-800 dark:text-gray-200">{{ number_format($linha['faixas'][$k] ?? 0, 0, ',', '.') }}</td>
                                @endforeach
                            @endif
                        </tr>
                    @empty
                        <tr><td class="px-3 py-6 text-center text-gray-500" colspan="{{ 2 + count($keysFaixa) }}">{{ __('Nenhum ocupante registrado ainda.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
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
        <section class="rounded-lg bg-white p-4 shadow dark:bg-gray-800">
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
