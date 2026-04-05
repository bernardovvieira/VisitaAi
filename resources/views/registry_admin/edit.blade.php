@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Editar tenant'))
@section('og_description', __('Atualizar ficha do tenant no registry.'))

@section('content')
<div class="v-page space-y-6">
    <x-breadcrumbs :items="[
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Registry de tenants'), 'url' => route('registry.admin.index')],
        ['label' => __('Editar')],
    ]" />

    <x-page-header :eyebrow="__('Registry')" :title="$tenant->slug" />

    <x-flash-alerts />

    <x-section-card class="dark:bg-gray-800">
        @if ($errors->has('update'))
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800 dark:border-red-900 dark:bg-red-950/40 dark:text-red-200">
                {{ $errors->first('update') }}
            </div>
        @endif

        <form method="POST" action="{{ route('registry.admin.update', $tenant) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('registry_admin._form')
            <div class="flex gap-3 pt-2">
                <x-primary-button type="submit">{{ __('Guardar') }}</x-primary-button>
                <a href="{{ route('registry.admin.index') }}" class="inline-flex items-center rounded-md border border-slate-300 px-3 py-2 text-sm dark:border-slate-600">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </x-section-card>
</div>
@endsection
