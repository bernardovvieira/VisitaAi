@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div class="flex justify-between items-center">
        <a href="{{ route('agente.visitas.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detalhes da Visita</h2>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Informações completas da visita epidemiológica registrada.
            </p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">Data da Visita</h3>
                <p class="text-base text-gray-900 dark:text-gray-100">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">Agente Responsável</h3>
                <p class="text-base text-gray-900 dark:text-gray-100">{{ $visita->usuario->use_nome }}</p>
            </div>
            <div>
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">Tipo de Visita</h3>
                <p class="text-base text-gray-900 dark:text-gray-100">
                    <span class="inline-block bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200 text-xs font-semibold px-2 py-1 rounded">
                        {{ $visita->vis_tipo }}
                    </span>
                </p>
            </div>
        </div>

        <div class="pt-4">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Local Visitado</h3>
            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                <span class="inline-block bg-purple-200 dark:bg-purple-800 text-purple-900 dark:text-purple-100 text-xs font-semibold px-2 py-1 rounded">
                    Cód. {{ $visita->local->loc_codigo_unico }}
                </span>
            </p>
            <p class="text-base text-gray-900 dark:text-gray-100 mt-1">
                {{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }} -
                {{ $visita->local->loc_bairro }}, {{ $visita->local->loc_cidade }}/{{ $visita->local->loc_estado }}<br>
                {{ $visita->local->loc_pais }}, {{ $visita->local->loc_cep }}
            </p>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                Coordenadas: {{ $visita->local->loc_latitude }}, {{ $visita->local->loc_longitude }}
            </p>
        </div>

        <div class="pt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Doenças Detectadas</h3>
            <div class="space-y-4 mt-2">
                @foreach ($visita->doencas as $doenca)
                    <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-lg border border-gray-300 dark:border-gray-600">
                        <h4 class="text-base font-bold text-blue-700 dark:text-blue-300">{{ $doenca->doe_nome }}</h4>

                        <div class="mt-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Sintomas:</span><br>
                            @if(is_array($doenca->doe_sintomas))
                                <div class="mt-1 space-y-1">
                                    @foreach($doenca->doe_sintomas as $item)
                                        <span class="inline-block bg-blue-200 dark:bg-blue-700 text-blue-900 dark:text-blue-100 text-xs font-medium px-2 py-0.5 rounded">{{ $item }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span>{{ $doenca->doe_sintomas }}</span>
                            @endif
                        </div>

                        <div class="mt-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Transmissão:</span><br>
                            @if(is_array($doenca->doe_transmissao))
                                <div class="mt-1 space-y-1">
                                    @foreach($doenca->doe_transmissao as $item)
                                        <span class="inline-block bg-yellow-200 dark:bg-yellow-700 text-yellow-900 dark:text-yellow-100 text-xs font-medium px-2 py-0.5 rounded">{{ $item }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span>{{ $doenca->doe_transmissao }}</span>
                            @endif
                        </div>

                        <div class="mt-2">
                            <span class="font-medium text-gray-700 dark:text-gray-300">Medidas de Controle:</span><br>
                            @if(is_array($doenca->doe_medidas_controle))
                                <div class="mt-1 space-y-1">
                                    @foreach($doenca->doe_medidas_controle as $item)
                                        <span class="inline-block bg-green-200 dark:bg-green-700 text-green-900 dark:text-green-100 text-xs font-medium px-2 py-0.5 rounded">{{ $item }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span>{{ $doenca->doe_medidas_controle }}</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="pt-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Observações</h3>
            <p class="text-base text-gray-900 dark:text-gray-100 mt-2">
                {{ $visita->vis_observacoes ?: 'Nenhuma observação registrada.' }}
            </p>
        </div>

        <div class="pt-6">
            <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
            <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                Localização exibida com base nas coordenadas registradas.
            </p>
        </div>
    </section>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = parseFloat("{{ $visita->local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $visita->local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
});
</script>
@endsection
