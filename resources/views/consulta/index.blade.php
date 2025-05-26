@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-10">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Consulta Pública</h1>
        <a href="{{ url('/') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    {{-- Busca por código --}}
    <form action="{{ route('consulta.matricula') }}" method="GET"
          class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
            Digite o <strong>código único do imóvel</strong> fornecido pelo agente
        </label>
        <div class="flex gap-4 flex-col md:flex-row">
            <input type="text" name="matricula" placeholder="Ex: 12345678" required
                   class="w-full rounded-md p-2 bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-md shadow transition">
                Consultar
            </button>
        </div>
    </form>

    {{-- Alerta de erro --}}
    @if (session('erro'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <strong class="font-bold">Erro:</strong>
            <span class="block sm:inline">{{ session('erro') }}</span>
        </div>
    @endif

    {{-- Mapa --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-3">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Mapa do Município</h2>
        <div id="mapa-publico" class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-700"></div>
        <p class="text-sm text-gray-600 dark:text-gray-400 italic">
            Este mapa está centrado na cidade onde as visitas estão sendo realizadas. Nenhum dado específico é exibido aqui.
        </p>
    </section>

    {{-- Tabela de doenças --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Doenças Monitoradas</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">
            Essas são as doenças cadastradas no sistema municipal, com seus sintomas, formas de transmissão e medidas de controle.
        </p>

        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                    <tr>
                        <th class="px-4 py-3 text-left">Doença</th>
                        <th class="px-4 py-3 text-left">Sintomas</th>
                        <th class="px-4 py-3 text-left">Transmissão</th>
                        <th class="px-4 py-3 text-left">Medidas de Controle</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                    @foreach($doencas as $doenca)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                            <td class="px-4 py-3 font-medium">{{ $doenca->doe_nome }}</td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_sintomas) ? implode(', ', $doenca->doe_sintomas) : $doenca->doe_sintomas }}
                            </td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_transmissao) ? implode(', ', $doenca->doe_transmissao) : $doenca->doe_transmissao }}
                            </td>
                            <td class="px-4 py-3">
                                {{ is_array($doenca->doe_medidas_controle) ? implode(', ', $doenca->doe_medidas_controle) : $doenca->doe_medidas_controle }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</div>

{{-- Leaflet --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const coordenadas = JSON.parse('{!! json_encode($coordenadas ?? ["lat" => -28.655, "lng" => -52.425]) !!}');

        const mapa = L.map('mapa-publico').setView([coordenadas.lat, coordenadas.lng], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(mapa);
    });
</script>
@endsection