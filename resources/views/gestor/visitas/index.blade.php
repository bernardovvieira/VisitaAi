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
            Aqui você pode visualizar e buscar visitas epidemiológicas registradas.
        </p>
    </section>

    <!-- Campo de Busca -->
    <section x-data="{ search: '{{ request('busca') }}' }" class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Buscar Visita</label>
                <input type="text" id="search" name="busca" x-model="search" x-init="$el.focus()"
                       @input.debounce.500ms="window.location.href = '{{ route('gestor.visitas.index') }}' + '?busca=' + encodeURIComponent(search)"
                       placeholder="Filtrar por local, agente ou doença..."
                       class="w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm px-4 py-2">
            </div>
        </div>
    </section>

    @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
        <section class="p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg shadow border border-yellow-300 dark:border-yellow-700">
            <h3 class="text-lg font-semibold text-yellow-800 dark:text-yellow-200 mb-2">
                Locais em que há pendências e não foram revisitados
            </h3>
            <ul class="space-y-2 text-sm text-yellow-900 dark:text-yellow-100 list-disc list-inside">
                @foreach ($locaisComPendenciasNaoRevisitadas as $local)
                    @php
                        $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                    @endphp
                    <li>
                        {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }} – {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                        <span class="text-xs text-yellow-700 dark:text-yellow-300 ml-2 italic">
                            Última pendência em {{ \Carbon\Carbon::parse($ultimaPendencia->vis_data)->format('d/m/Y') }}
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    <!-- Contador de resultados -->
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
            Exibindo {{ $visitas->count() }} de {{ $visitas->total() }} visita(s) registradas.
            @if(request('busca'))
                <span class="text-gray-500">Resultados para: <strong>{{ request('busca') }}</strong></span>
            @endif
        </p>

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Código</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Data</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Pendência</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Atividade</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Local</th>
                        <th class="p-4 text-left text-sm font-semibold text-gray-700 dark:text-gray-300">Agente</th>
                        <th class="p-4 text-center text-sm font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($visitas as $visita)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                    #{{ $visita->vis_id }}
                                </span>
                            </td>
                           <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                                <div class="font-semibold">
                                    {{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                @if($visita->vis_pendencias)
                                    <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                        Pendente
                                    </span>

                                    @php
                                        $revisitaPosterior = $visita->local->visitas()
                                            ->where('vis_data', '>', $visita->vis_data)
                                            ->orderBy('vis_data')
                                            ->first();
                                    @endphp

                                    @if($revisitaPosterior)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                            Revisitado em {{ \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                        Concluída
                                    </span>
                                @endif
                            </td>
                            @php
                                $atividades = [
                                    '1' => 'LI',
                                    '2' => 'LI+T',
                                    '3' => 'PPE+T',
                                    '4' => 'T',
                                    '5' => 'DF',
                                    '6' => 'PVE',
                                    '7' => 'LIRAa',
                                    '8' => 'PE',
                                ];
                            @endphp

                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $atividades[$visita->vis_atividade] ?? 'Não informado' }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                                <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Bairro: {{ $visita->local->loc_bairro }}<br>
                                    Cód: {{ $visita->local->loc_codigo_unico }}
                                </div>
                            </td>
                            <td class="p-4 text-gray-800 dark:text-gray-100">
                                <div class="font-semibold">{{ $visita->usuario->use_nome }}</div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    {{ $visita->usuario->use_perfil == 'agente_endemias' ? 'Agente de Endemias' : 'Agente de Saúde' }}
                                </div>
                            </td>
                            <td class="p-4 text-center">
                                <div class="flex justify-center gap-3">
                                    <a href="{{ route('gestor.visitas.show', $visita) }}"
                                       class="inline-flex items-center gap-2 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                                       <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        Visualizar
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-6 text-center text-gray-600 dark:text-gray-400">Nenhuma visita registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $visitas->appends(request()->query())->links() }}
        </div>
    </section>
</div>
@endsection