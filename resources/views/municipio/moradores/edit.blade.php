@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => 'Locais', 'url' => route($profile . '.locais.index')],
        ['label' => 'Visualizar', 'url' => route($profile . '.locais.show', $local)],
        ['label' => 'Moradores', 'url' => route($profile . '.locais.moradores.index', $local)],
        ['label' => __('Editar')],
    ])" />

    <a href="{{ route($profile . '.locais.moradores.index', $local) }}"
       class="inline-flex items-center rounded-xl border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm font-semibold text-gray-800 shadow-sm transition hover:bg-gray-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/50 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
        {{ __('Voltar') }}
    </a>

    <section class="space-y-4 rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800">
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
                <a href="{{ route($profile . '.locais.moradores.index', $local) }}" class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">{{ __('Cancelar') }}</a>
            </div>
        </form>
    </section>
</div>
@endsection
