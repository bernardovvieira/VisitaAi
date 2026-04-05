@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Novo ocupante'))
@section('og_description', __('Cadastrar ocupante no imóvel selecionado.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="array_filter([
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Locais'), 'url' => route($profile . '.locais.index')],
        ['label' => __('Visualizar'), 'url' => route($profile . '.locais.show', $local)],
        ['label' => __('Ocupantes'), 'url' => route($profile . '.locais.moradores.index', $local)],
        ['label' => __('Novo')],
    ])" />

    <x-section-card class="w-full space-y-4">
        <x-page-header :eyebrow="__('Ocupantes')" :title="__('Novo ocupante')" />
        <form method="post" action="{{ route($profile . '.locais.moradores.store', $local) }}" class="space-y-6">
            @csrf
            @include('municipio.moradores._form')
            <div class="flex flex-wrap gap-3 pt-2">
                <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                <a href="{{ route($profile . '.locais.moradores.index', $local) }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-[13px] font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </x-section-card>
</div>
@endsection
