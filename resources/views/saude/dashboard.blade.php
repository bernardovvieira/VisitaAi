@extends('layouts.app')

@section('og_title', config('app.name') . ' · Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_saude'))
@section('og_description', 'Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_saude') . '. Registre visitas LIRAa e consulte doenças monitoradas. Conforme Lei 11.350/2006 e Diretriz MS.')

@section('content')
@php
    $uid = Auth::user()->use_id;
    $visitasLira = \App\Models\Visita::where('fk_usuario_id', $uid)->where('vis_atividade', '7')->count();
    $doencasCount = \App\Models\Doenca::count();
@endphp

<div class="v-page">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="v-page-header">
        <h1 class="v-page-title">{{ __('Painel do :perfil', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_saude')]) }}</h1>
        <p class="v-page-lead">
            {{ __('Olá, :nome. Central LIRAa — visitas de levantamento e cadastro de imóveis.', ['nome' => Auth::user()->use_nome]) }}
            <span class="mt-1 block text-xs font-semibold text-slate-700 dark:text-slate-300">LIRAa</span>
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-500">{{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</span>
        </p>
    </header>

    <div class="v-panel">
        <div class="v-panel-section">
            <h2 class="v-toolbar-label mb-3">{{ __('Resumo') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-clipboard-document-list class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Visitas LIRAa') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $visitasLira }}</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ __('Registradas por você') }}</p>
                    </div>
                </div>
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-beaker class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Doenças monitoradas') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $doencasCount }}</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ __('Referência municipal') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="v-panel-section-muted">
            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                {{ __('Sem internet, guarde a visita no dispositivo e envie tudo quando voltar a ficar online pela página de sincronização.') }}
            </p>
        </div>

        <div class="v-panel-section">
            <h2 class="v-toolbar-label mb-3">{{ __('Ações rápidas') }}</h2>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('saude.visitas.create') }}" class="v-dashboard-action v-dashboard-action--primary">
                    <x-heroicon-o-plus-circle class="h-5 w-5 shrink-0" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Nova visita LIRAa') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('saude.visitas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Minhas visitas') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('saude.doencas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Doenças') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('saude.sincronizar') }}" class="v-dashboard-action">
                    <x-heroicon-o-arrow-path class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Sincronizar dados') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
