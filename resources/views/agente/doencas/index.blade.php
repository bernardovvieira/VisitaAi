<!-- resources/views/agente/doencas/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Doenças'))
@section('og_description', __('Doenças monitoradas. Consulte as doenças que você pode registrar nas visitas de vigilância entomológica e controle vetorial.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças')]]" />
    <x-page-header :eyebrow="__('Referência epidemiológica')" :title="__('Doenças monitoradas')">
        <x-slot name="lead">
            <p>{{ __('Consulte as doenças que podem ser registradas nas visitas. Use o botão Ver para detalhes.') }}</p>
        </x-slot>
    </x-page-header>

    <x-section-card class="v-card--muted">
        <x-form-field name="search" :label="__('Busca inteligente')">
            <div class="flex items-center gap-2">
                <input type="text" name="search" id="search" value="{{ old('search', request('search')) }}"
                       data-live-url="{{ route('agente.doencas.index') }}" data-live-param="search"
                       data-live-loading-id="search-loading-doencas"
                       placeholder="{{ __('Nome, sintomas, transmissão ou medidas…') }}"
                       class="v-input" />
                <span id="search-loading-doencas" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
            </div>
        </x-form-field>
    </x-section-card>

    <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total :item.', ['atual' => $doencas->count(), 'total' => $doencas->total(), 'item' => $doencas->total() === 1 ? __('doença monitorada') : __('doenças monitoradas')]) }}
                @if(request('search'))
                    <span class="text-slate-500 dark:text-slate-500">{{ __('Resultados para:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('search') }}</strong></span>
                @endif
            </span>
        </div>
        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('ID') }}</th>
                        <th scope="col">{{ __('Nome') }}</th>
                        <th scope="col">{{ __('Sintomas') }}</th>
                        <th scope="col">{{ __('Transmissão') }}</th>
                        <th scope="col">{{ __('Medidas de controle') }}</th>
                        <th scope="col" class="text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($doencas as $doenca)
                        <tr>
                            <td>
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">#{{ $doenca->doe_id }}</span>
                            </td>
                            <td class="font-medium text-slate-900 dark:text-slate-100">{{ $doenca->doe_nome }}</td>
                            <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_sintomas), 30) }}</td>
                            <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_transmissao), 30) }}</td>
                            <td class="text-slate-700 dark:text-slate-300">{{ Str::limit(implode(', ', $doenca->doe_medidas_controle), 30) }}</td>
                            <td class="text-center">
                                @can('view', $doenca)
                                <a href="{{ route('agente.doencas.show', $doenca) }}"
                                   class="v-btn-icon-primary"
                                   title="{{ __('Visualizar') }}"
                                   aria-label="{{ __('Visualizar doença') }}">
                                   <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                </a>
                                @else
                                    <span class="text-xs italic text-slate-500 dark:text-slate-400">{{ __('Sem acesso') }}</span>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <x-empty-state
                                    :title="__('Nenhuma doença monitorada disponível.')"
                                    :description="__('Verifique os termos de busca ou retorne em outro momento.')"
                                    icon="heroicon-o-beaker"
                                    class="border-0 bg-transparent px-4"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$doencas" item-label="doenças" />
    </x-section-card>
</div>
@endsection