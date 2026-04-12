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

    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
        <header class="v-dash-header min-w-0 flex-1">
            <div class="v-dash-header-text">
                <p class="v-dash-eyebrow">{{ __('Operações de campo') }}</p>
                <h1 class="v-dash-title">{{ __('Olá, :nome', ['nome' => $primeiroNome]) }}</h1>
                <p class="v-dash-sub">{{ __('Registre visitas, mantenha locais e complete dados complementares do imóvel quando fizer parte do fluxo local; use os atalhos para o que for mais comum.') }}</p>
            </div>
        </header>

        <div class="flex shrink-0 flex-wrap justify-end gap-2 lg:pt-1">
            <a href="{{ route('agente.visitas.create') }}" class="v-btn-compact v-btn-compact--blue">
                <x-heroicon-o-plus-circle class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Registrar visita') }}
            </a>
            <a href="{{ route('agente.visitas.index') }}" class="v-btn-compact v-btn-compact--slate">
                <x-heroicon-o-clipboard-document-list class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Minhas visitas') }}
            </a>
            <a href="{{ route('agente.locais.index') }}" class="v-btn-compact v-btn-compact--slate">
                <x-heroicon-o-map-pin class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Locais') }}
            </a>
            <a href="{{ route('agente.doencas.index') }}" class="v-btn-compact v-btn-compact--slate">
                <x-heroicon-o-beaker class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Doenças monitoradas') }}
            </a>
        </div>
    </div>

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

    <p class="mt-4 text-sm leading-relaxed text-slate-600 dark:text-slate-400">
        {{ __('As visitas podem ser preenchidas offline e enviadas depois pela sincronização.') }}
    </p>
</div>
@endsection
