<!-- resources/views/gestor/doencas/index.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · Doenças')
@section('og_description', 'Doenças monitoradas no município. Visualize, edite e cadastre doenças para as visitas de vigilância entomológica e controle vetorial.')

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças')]]" />
    <x-page-header :eyebrow="__('Cadastros municipais')" :title="__('Doenças')">
        <x-slot name="lead">
            <p>{{ __('Visualize, edite ou exclua doenças cadastradas para vigilância municipal. Inclua novas para orientar o trabalho em campo.') }}</p>
        </x-slot>
    </x-page-header>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="v-card">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="v-section-title">{{ __('Doenças monitoradas') }}</h2>
                <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">{{ __('Use a busca para filtrar por nome, sintomas, transmissão ou medidas de controle.') }}</p>
            </div>
            <a href="{{ route('gestor.doencas.create') }}"
               class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/40 dark:bg-blue-600 dark:hover:bg-blue-500">
                <x-heroicon-o-plus class="h-5 w-5 shrink-0" />
                {{ __('Cadastrar doença') }}
            </a>
        </div>
    </div>

    <div class="v-card v-card--muted">
        <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
        <div class="mt-1 flex items-center gap-2">
            <input type="text" name="search" id="search" value="{{ old('search', request('search')) }}"
                   data-live-url="{{ route('gestor.doencas.index') }}" data-live-param="search"
                   data-live-loading-id="search-loading-doencas"
                   placeholder="{{ __('Nome, sintomas, transmissão ou medidas…') }}"
                   class="v-input" />
            <span id="search-loading-doencas" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
        </div>
    </div>

    <div class="v-card v-card--flush overflow-hidden">
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total doença(s) cadastrada(s).', ['atual' => $doencas->count(), 'total' => $doencas->total()]) }}
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
                                <div class="flex justify-center gap-1.5">
                                    <a href="{{ route('gestor.doencas.show', $doenca) }}"
                                       class="btn-acesso-principal inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg shadow-sm transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-slate-900"
                                       title="{{ __('Visualizar') }}"
                                       aria-label="{{ __('Visualizar doença') }}">
                                       <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                    </a>
                                    <a href="{{ route('gestor.doencas.edit', $doenca) }}"
                                        class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                        title="{{ __('Editar') }}"
                                        aria-label="{{ __('Editar doença') }}">
                                        <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                    </a>
                                    <form method="POST" action="{{ route('gestor.doencas.destroy', $doenca) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm(@json(__('Tem certeza que deseja excluir esta doença? Esta ação não pode ser desfeita.')))"
                                                class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                                                title="{{ __('Excluir') }}"
                                                aria-label="{{ __('Excluir doença') }}">
                                                <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="!p-0">
                                <div class="v-empty-state px-4">
                                    <div class="v-empty-state__icon" aria-hidden="true">
                                        <x-heroicon-o-beaker class="h-7 w-7 shrink-0" />
                                    </div>
                                    <p class="v-empty-state__title">{{ __('Nenhuma doença cadastrada no município.') }}</p>
                                    <p class="v-empty-state__text">{{ __('Cadastre as doenças monitoradas para orientar ACE e ACS em campo.') }}</p>
                                    <a href="{{ route('gestor.doencas.create') }}" class="mt-5 inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50">
                                        <x-heroicon-o-plus class="h-4 w-4 shrink-0" />
                                        {{ __('Cadastrar doença') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <x-pagination-relatorio :paginator="$doencas" item-label="doenças" />
    </div>
</div>
@endsection
