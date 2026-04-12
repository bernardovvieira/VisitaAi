@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Nova doença'))
@section('og_description', __('Cadastro municipal de doença monitorada e sintomas associados.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças'), 'url' => route('gestor.doencas.index')], ['label' => __('Novo')]]" />

    <x-page-header :eyebrow="__('Cadastros municipais')" :title="__('Nova doença')" />

    <x-section-card class="w-full space-y-4">
        <x-flash-alerts />

        @include('gestor.doencas._form', [
            'formAction' => route('gestor.doencas.store'),
            'formMethod' => 'POST',
            'formId' => 'doenca-create-form',
            'submitId' => 'doenca-create-btn',
            'submitLabel' => __('Salvar'),
        ])
    </x-section-card>
</div>
@endsection