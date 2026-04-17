@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Locais') . ' · ' . $local->loc_codigo_unico)
@section('og_description', __('Detalhes do imóvel cadastrado: endereço, código único e dados de visitação.'))

@section('content')
@php
    $numeroLead = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : __('S/N');
    $fichaPdfUrl = $fichaPdfUrl ?? route('gestor.locais.ficha-socioeconomica-pdf', $local);
@endphp
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais'), 'url' => route('gestor.locais.index')], ['label' => __('Visualizar')]]" />

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <x-page-header :eyebrow="__('Locais')" :title="__('Imóvel cadastrado')">
                <x-slot name="lead">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        <span class="font-mono font-semibold text-slate-900 dark:text-slate-100">#{{ $local->loc_codigo_unico }}</span>
                        <span class="text-slate-400"> · </span>
                        {{ $local->loc_endereco }}, {{ $numeroLead }}, {{ $local->loc_bairro }}
                    </p>
                </x-slot>
            </x-page-header>
        </div>
        <div class="flex shrink-0 items-center gap-2 self-start">
            <a href="{{ $fichaPdfUrl }}" class="v-btn-compact v-btn-compact--red">
                <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0" aria-hidden="true" />
                {{ __('Ficha socioeconômica') }}
            </a>
        </div>
    </div>

    @include('municipio.locais._show_shared', [
        'local' => $local,
        'qrCodeBase64' => $qrCodeBase64,
        'qrCodeMime' => $qrCodeMime,
        'fichaPdfUrl' => route('gestor.locais.ficha-socioeconomica-pdf', $local),
        'moradorResumo' => $moradorResumo,
        'arquivosZonaSemBordaLateral' => true,
    ])
</div>
@endsection
