@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Editar doença'))
@section('og_description', __('Edição de cadastro municipal de doença e sintomas.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Doenças'), 'url' => route('gestor.doencas.index')], ['label' => __('Editar')]]" />

    <x-page-header :eyebrow="__('Cadastros municipais')" :title="__('Editar doença')" />

    <x-section-card class="w-full space-y-4">
        <x-flash-alerts />

        @include('gestor.doencas._form', [
            'doenca' => $doenca,
            'formAction' => route('gestor.doencas.update', $doenca),
            'formMethod' => 'PATCH',
            'formId' => 'doenca-edit-form',
            'submitId' => 'doenca-edit-btn',
            'submitLabel' => __('Salvar alterações'),
        ])
    </x-section-card>
</div>
@endsection