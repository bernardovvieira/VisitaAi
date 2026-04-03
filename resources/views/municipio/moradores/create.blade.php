@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => 'Locais', 'url' => route($profile . '.locais.index')],
        ['label' => 'Visualizar', 'url' => route($profile . '.locais.show', $local)],
        ['label' => 'Moradores', 'url' => route($profile . '.locais.moradores.index', $local)],
        ['label' => 'Novo'],
    ])" />

    <div>
        <a href="{{ route($profile . '.locais.moradores.index', $local) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            Voltar
        </a>
    </div>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4">
        <p class="text-xs text-amber-800 dark:text-amber-200/90 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-md px-3 py-2">
            {{ config('visitaai_municipio.ocupantes.disclaimer') }}
        </p>
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Novo ocupante</h1>
        <form method="post" action="{{ route($profile . '.locais.moradores.store', $local) }}" class="space-y-6">
            @csrf
            @include('municipio.moradores._form')
            <div class="flex gap-3">
                <x-primary-button>{{ __('Salvar') }}</x-primary-button>
                <a href="{{ route($profile . '.locais.moradores.index', $local) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded-md text-sm">Cancelar</a>
            </div>
        </form>
    </section>
</div>
@endsection
