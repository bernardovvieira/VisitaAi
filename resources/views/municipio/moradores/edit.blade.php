@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Locais'), 'url' => route($profile . '.locais.index')],
        ['label' => __('Visualizar'), 'url' => route($profile . '.locais.show', $local)],
        ['label' => 'Moradores', 'url' => route($profile . '.locais.moradores.index', $local)],
        ['label' => __('Editar')],
    ])" />

    <section class="v-card space-y-4">
        <p class="rounded-lg border border-amber-200/90 bg-amber-50 px-3 py-2.5 text-xs leading-relaxed text-amber-950 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100">
            {{ config('visitaai_municipio.ocupantes.disclaimer') }}
        </p>
        <h1 class="text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-100">{{ __('Editar ocupante') }}</h1>
        <form method="post" action="{{ route($profile . '.locais.moradores.update', [$local, $morador]) }}" class="space-y-6">
            @csrf
            @method('patch')
            @include('municipio.moradores._form')
            <div class="flex flex-wrap gap-3 pt-2">
                <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                <a href="{{ route($profile . '.locais.moradores.index', $local) }}" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-1.5 text-[13px] font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </section>
</div>
@endsection
