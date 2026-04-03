@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="array_filter([
        ['label' => 'Página Inicial', 'url' => route('dashboard')],
        ['label' => 'Locais', 'url' => route($profile . '.locais.index')],
        ['label' => 'Visualizar', 'url' => route($profile . '.locais.show', $local)],
        ['label' => 'Moradores'],
    ])" />

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route($profile . '.locais.show', $local) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar ao local
        </a>
        <a href="{{ route($profile . '.locais.moradores.create', $local) }}"
           class="inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow transition">
            Cadastrar ocupante
        </a>
    </div>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow space-y-2">
        <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-200">{{ config('visitaai_municipio.ocupantes.titulo_listagem') }}</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Código do imóvel: <span class="font-mono font-semibold">{{ $local->loc_codigo_unico }}</span>
            — {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}
        </p>
        <p class="text-xs text-amber-800 dark:text-amber-200/90 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-700 rounded-md px-3 py-2">
            {{ config('visitaai_municipio.ocupantes.disclaimer') }}
        </p>
    </section>

    <div class="bg-white dark:bg-gray-700 rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-600 text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Nome / identificação</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Nascimento</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Idade</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-700 dark:text-gray-300">Escolaridade</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-600">
                    @forelse ($moradores as $m)
                        <tr class="text-gray-800 dark:text-gray-200">
                            <td class="px-4 py-3">{{ $m->mor_nome ?: '—' }}</td>
                            <td class="px-4 py-3">{{ $m->mor_data_nascimento ? $m->mor_data_nascimento->format('d/m/Y') : '—' }}</td>
                            <td class="px-4 py-3">{{ $m->idadeAnos() !== null ? $m->idadeAnos() . ' anos' : '—' }}</td>
                            <td class="px-4 py-3">{{ $m->mor_escolaridade ? (config('visitaai_municipio.escolaridade_opcoes.' . $m->mor_escolaridade) ?: $m->mor_escolaridade) : '—' }}</td>
                            <td class="px-4 py-3 text-right space-x-2">
                                <a href="{{ route($profile . '.locais.moradores.edit', [$local, $m]) }}" class="text-blue-600 dark:text-blue-400 hover:underline">Editar</a>
                                <form action="{{ route($profile . '.locais.moradores.destroy', [$local, $m]) }}" method="post" class="inline" onsubmit="return confirm('Excluir este registro de ocupante?');">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="text-red-600 dark:text-red-400 hover:underline">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">Nenhum ocupante registrado neste imóvel.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($moradores->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-600">{{ $moradores->links() }}</div>
        @endif
    </div>
</div>
@endsection
