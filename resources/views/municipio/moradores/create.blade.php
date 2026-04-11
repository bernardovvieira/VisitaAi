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
        <div class="rounded-xl border border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50 p-4 shadow-sm dark:border-amber-700/70 dark:from-amber-950/40 dark:to-orange-950/30">
            <div class="flex items-start gap-3">
                <x-heroicon-o-shield-check class="mt-0.5 h-5 w-5 shrink-0 text-amber-700 dark:text-amber-300" aria-hidden="true" />
                <div class="space-y-1">
                    <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">{{ __('Termo de ciência LGPD e tratamento de dados') }}</p>
                    <p class="text-xs leading-relaxed text-amber-900/90 dark:text-amber-100/90">
                        {{ __('Ao preencher este cadastro, o agente e o ocupante declaram ciência e concordância com o tratamento dos dados pessoais para fins informativos e de gestão municipal, incluindo compartilhamento controlado com terceiros em ações municipais, conforme a LGPD.') }}
                    </p>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route($profile . '.locais.moradores.store', $local) }}" enctype="multipart/form-data" class="space-y-6">
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
