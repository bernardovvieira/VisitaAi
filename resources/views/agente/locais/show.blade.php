@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Locais') . ' · ' . $local->loc_codigo_unico)
@section('og_description', __('Detalhes do imóvel cadastrado: endereço, código único e dados de visitação.'))

@section('content')
@php
    $numeroLead = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : __('S/N');
    $fichaPdfUrl = auth()->user()->isAgenteEndemias()
        ? route('agente.locais.ficha-socioeconomica-pdf', $local)
        : null;
@endphp
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais'), 'url' => route('agente.locais.index')], ['label' => __('Visualizar')]]" />

    <x-page-header :eyebrow="__('Locais')" :title="__('Imóvel cadastrado')">
        <x-slot name="lead">
            <p class="text-sm text-slate-600 dark:text-slate-400">
                <span class="font-mono font-semibold text-slate-900 dark:text-slate-100">{{ $local->loc_codigo_unico }}</span>
                <span class="text-slate-400"> · </span>
                {{ $local->loc_endereco }}, {{ $numeroLead }}, {{ $local->loc_bairro }}
            </p>
        </x-slot>
    </x-page-header>

    @include('municipio.locais._show_shared', [
        'local' => $local,
        'qrCodeBase64' => $qrCodeBase64,
        'qrCodeMime' => $qrCodeMime,
        'fichaPdfUrl' => $fichaPdfUrl,
    ])

    @include('municipio.moradores._resumo-local', ['local' => $local, 'moradorResumo' => $moradorResumo])
</div>
@endsection
