@extends('layouts.app')

@section('og_title', config('app.name') . ' · Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_endemias'))
@section('og_description', 'Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_endemias') . '. Registre visitas, gerencie locais e consulte doenças monitoradas. Conforme Lei 11.350/2006 e Diretriz MS.')

@section('content')
@php
    $uid = Auth::user()->use_id;
    $minhasVisitas = \App\Models\Visita::where('fk_usuario_id', $uid)->count();
    $visitasMunicipio = \App\Models\Visita::count();
    $locaisCount = \App\Models\Local::count();
    $doencasCount = \App\Models\Doenca::count();
@endphp

<div class="v-page">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <x-page-header :eyebrow="__('Operações de campo')" :title="__('Painel do :perfil', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_endemias')])">
        <x-slot name="lead">
            <p>{{ __('Olá, :nome. Registre visitas e mantenha locais atualizados em campo.', ['nome' => Auth::user()->use_nome]) }}</p>
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-500">{{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</span>
        </x-slot>
    </x-page-header>

        <div class="v-card">
            <h2 class="v-toolbar-label mb-3">{{ __('Resumo') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-clipboard-document-list class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Minhas visitas') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $minhasVisitas }}</p>
                    </div>
                </div>
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-check-circle class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Visitas no município') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $visitasMunicipio }}</p>
                    </div>
                </div>
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-map-pin class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Locais') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $locaisCount }}</p>
                    </div>
                </div>
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-beaker class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Doenças') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $doencasCount }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="v-card v-card--muted">
            <p class="text-sm leading-relaxed text-slate-600 dark:text-slate-400">
                {{ __('As visitas podem ser preenchidas offline e enviadas depois pela sincronização. Use os atalhos abaixo para o fluxo mais comum.') }}
            </p>
        </div>

        <div class="v-card">
            <h2 class="v-toolbar-label mb-3">{{ __('Ações rápidas') }}</h2>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('agente.visitas.create') }}" class="v-dashboard-action v-dashboard-action--primary">
                    <x-heroicon-o-plus-circle class="h-5 w-5 shrink-0" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Registrar visita') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('agente.visitas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Minhas visitas') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('agente.locais.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Locais') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('agente.doencas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Doenças monitoradas') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
            </div>
        </div>
</div>
@endsection
