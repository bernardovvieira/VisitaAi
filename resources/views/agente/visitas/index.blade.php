@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Visitas Realizadas</h1>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Informações</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Abaixo estão listadas todas as visitas registradas por você. Utilize o botão para registrar uma nova visita.
        </p>
        <a href="{{ route('agente.visitas.create') }}"
           class="inline-flex items-center px-4 py-2 mt-4 bg-green-600 hover:bg-green-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Registrar Visita
        </a>
    </section>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Local</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Doenças</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Agente</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visitas as $visita)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @foreach($visita->doencas as $doenca)
                                    <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300 mr-1 mb-1">
                                        {{ $doenca->doe_nome }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">{{ $visita->usuario->use_nome }}</td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('agente.visitas.show', $visita) }}"
                                       class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg shadow transition">
                                       Ver
                                    </a>
                                    <a href="{{ route('agente.visitas.edit', $visita) }}"
                                       class="px-3 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded-lg shadow transition">
                                       Editar
                                    </a>
                                    <form method="POST" action="{{ route('agente.visitas.destroy', $visita) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Excluir esta visita?')"
                                                class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-lg shadow transition">
                                            Excluir
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhuma visita registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $visitas->links() }}
        </div>
    </section>
</div>
@endsection