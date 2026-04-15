@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Painel do :perfil', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_saude')]))
@section('og_description', __('Painel do :perfil. Registre visitas, gerencie locais e consulte doenças monitoradas. Conforme Lei 11.350/2006 e Diretriz MS.', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_saude')]))

@section('content')
@php
    $primeiroNome = strtok((string) Auth::user()->use_nome, ' ') ?: Auth::user()->use_nome;
    $uid = Auth::user()->use_id;
    $visitasRegistradas = \App\Models\Visita::where('fk_usuario_id', $uid)->where('vis_atividade', '7')->count();
    $imoveisNoSistema = \App\Models\Local::count();
    $doencasCount = \App\Models\Doenca::count();
@endphp

<div class="v-dash">
    <x-breadcrumbs :items="[['label' => __('Página Inicial')]]" />

    <header class="v-dash-header">
        <div class="v-dash-header-text">
            <p class="v-dash-eyebrow">{{ __('Painel territorial') }}</p>
            <h1 class="v-dash-title">{{ __('Olá, :nome', ['nome' => $primeiroNome]) }}</h1>
            <p class="v-dash-sub">{{ __('Cadastro de imóveis, visitas no território e sincronização offline quando precisar.') }}</p>
        </div>
    </header>

    <section class="v-dash-card" aria-labelledby="saude-resumo-heading">
        <h2 id="saude-resumo-heading" class="v-dash-card__title">{{ __('Resumo') }}</h2>
        <div class="v-kpi-grid-agi v-kpi-grid-agi--3 mt-4">
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Visitas registradas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $visitasRegistradas }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Registradas por você') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Imóveis no sistema') }}</span>
                <span class="v-kpi-card-agi__value">{{ $imoveisNoSistema }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Números consolidados do município.') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Doenças monitoradas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $doencasCount }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Referência municipal') }}</span>
            </div>
        </div>
    </section>

    <section class="v-dash-card" aria-labelledby="saude-atalhos-heading">
        <h2 id="saude-atalhos-heading" class="v-dash-card__title">{{ __('Ações rápidas') }}</h2>
        <div class="v-dash-shortcuts v-dash-shortcuts--tight mt-4">
            <a href="{{ route('saude.visitas.create') }}" class="v-dash-shortcut v-dash-shortcut--primary">
                <x-heroicon-o-plus-circle class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Nova visita') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('saude.visitas.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-clipboard-document-list class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Minhas visitas') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('saude.locais.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-map-pin class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Locais') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('saude.doencas.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-beaker class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Doenças') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('saude.sincronizar') }}" class="v-dash-shortcut">
                <x-heroicon-o-arrow-path class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Sincronizar dados') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </section>
</div>
@endsection
