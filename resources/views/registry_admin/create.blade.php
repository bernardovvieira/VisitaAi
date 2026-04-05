@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Novo tenant'))
@section('og_description', __('Registar slug e base MySQL no registry.'))

@section('content')
<div class="v-page space-y-6">
    <x-breadcrumbs :items="[
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Registry de tenants'), 'url' => route('registry.admin.index')],
        ['label' => __('Novo')],
    ]" />

    <x-page-header :eyebrow="__('Registry')" :title="__('Novo tenant')" />

    <x-flash-alerts />

    <x-section-card class="dark:bg-gray-800">
        @if ($errors->has('store'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
                {{ $errors->first('store') }}
            </div>
        @endif

        <form method="POST" action="{{ route('registry.admin.store') }}" class="space-y-6">
            @csrf
            @include('registry_admin._form')
            <div class="flex gap-3 pt-2">
                <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                <a href="{{ route('registry.admin.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </x-section-card>
</div>
@endsection

