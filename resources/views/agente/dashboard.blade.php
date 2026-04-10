@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Painel do :perfil', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_endemias')]))
@section('og_description', __('Painel do :perfil. Visitas de campo, PNCD conforme habilitação municipal, locais e cadastro complementar do imóvel. Use o que o município definiu. Lei 11.350/2006 e diretrizes MS.', ['perfil' => \App\Helpers\MsTerminologia::perfilLabel('agente_endemias')]))

@section('content')
@php
    $primeiroNome = strtok((string) Auth::user()->use_nome, ' ') ?: Auth::user()->use_nome;
    $uid = Auth::user()->use_id;
    $minhasVisitas = \App\Models\Visita::where('fk_usuario_id', $uid)->count();
    $visitasMunicipio = \App\Models\Visita::count();
    $locaisCount = \App\Models\Local::count();
    $doencasCount = \App\Models\Doenca::count();
@endphp

<div class="v-dash">
    <x-breadcrumbs :items="[['label' => __('Página Inicial')]]" />

    <header class="v-dash-header">
        <div class="v-dash-header-text">
            <p class="v-dash-eyebrow">{{ __('Operações de campo') }}</p>
            <h1 class="v-dash-title">{{ __('Olá, :nome', ['nome' => $primeiroNome]) }}</h1>
            <p class="v-dash-sub">{{ __('Registre visitas, mantenha locais e complete dados complementares do imóvel quando fizer parte do fluxo local; use os atalhos para o que for mais comum.') }}</p>
        </div>
    </header>

    <section class="v-dash-card" aria-labelledby="agente-resumo-heading">
        <h2 id="agente-resumo-heading" class="v-dash-card__title">{{ __('Resumo') }}</h2>
        <p class="v-dash-card__sub">{{ __('Suas visitas e o panorama municipal.') }}</p>
        <div class="v-kpi-grid-agi v-kpi-grid-agi--dense mt-4">
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Minhas visitas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $minhasVisitas }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Visitas no município') }}</span>
                <span class="v-kpi-card-agi__value">{{ $visitasMunicipio }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Locais') }}</span>
                <span class="v-kpi-card-agi__value">{{ $locaisCount }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Doenças') }}</span>
                <span class="v-kpi-card-agi__value">{{ $doencasCount }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Referência municipal') }}</span>
            </div>
        </div>
    </section>

    <div class="v-dash-card">
        <p class="m-0 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
            {{ __('As visitas podem ser preenchidas offline e enviadas depois pela sincronização.') }}
        </p>
    </div>

    <section class="v-dash-card" aria-labelledby="agente-atalhos-heading">
        <h2 id="agente-atalhos-heading" class="v-dash-card__title">{{ __('Ações rápidas') }}</h2>
        <div class="v-dash-shortcuts v-dash-shortcuts--tight mt-4">
            <a href="{{ route('agente.visitas.create') }}" class="v-dash-shortcut v-dash-shortcut--primary">
                <x-heroicon-o-plus-circle class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Registrar visita') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('agente.visitas.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-clipboard-document-list class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Minhas visitas') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('agente.locais.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-map-pin class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Locais') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
            <a href="{{ route('agente.doencas.index') }}" class="v-dash-shortcut">
                <x-heroicon-o-beaker class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                <span class="v-dash-shortcut__body">
                    <span class="v-dash-shortcut__label">{{ __('Doenças monitoradas') }}</span>
                </span>
                <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
            </a>
        </div>
    </section>
</div>
@endsection
