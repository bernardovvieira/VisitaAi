@extends('layouts.app')

@section('public')
@endsection

@section('content')
<div class="container mx-auto p-6 space-y-8 max-w-6xl">

    {{-- Botão de Voltar --}}
    <div>
        <a href="{{ route('consulta.index') }}"
           class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar à consulta
        </a>
    </div>

    {{-- Título principal --}}
    <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
        Resultado da Consulta Pública
    </h1>

    {{-- Informações do local e mapa --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div class="space-y-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Endereço Consultado</h2>
            <div class="text-sm text-gray-800 dark:text-gray-100 leading-relaxed space-y-1">
                <p><strong>CEP:</strong> {{ $local->loc_cep }}</p>
                <p><strong>Endereço:</strong> {{ $local->loc_endereco }}, {{ $local->loc_numero }}</p>
                <p><strong>Bairro:</strong> {{ $local->loc_bairro }}</p>
                <p><strong>Cidade:</strong> {{ $local->loc_cidade }}/{{ $local->loc_estado }}</p>
                <p><strong>Matrícula:</strong> {{ $local->loc_codigo_unico }}</p>
                <p><strong>Latitude:</strong> {{ $local->loc_latitude }}</p>
                <p><strong>Longitude:</strong> {{ $local->loc_longitude }}</p>
                <p class="text-xs text-gray-500 italic">* As informações de coordenadas podem não ser exatas, dependendo do cadastro do imóvel.</p>
            </div>
        </div>
        <div class="w-full h-64 rounded-lg overflow-hidden border" id="mapa-local"></div>
    </section>

    {{-- Histórico de Visitas --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Histórico de Visitas</h2>

        @if ($visitas->isEmpty())
            <p class="text-sm text-gray-600 dark:text-gray-400 italic">Nenhuma visita registrada neste endereço até o momento.</p>
        @else
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Dia da Semana</th>
                            <th class="px-4 py-3 text-left">Doenças Identificadas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                        @foreach ($visitas as $visita)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <td class="px-4 py-3 whitespace-nowrap">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</td>
                                <td class="px-4 py-3">
                                    @forelse($visita->doencas as $doenca)
                                        <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-300 mr-1 mb-1">
                                            {{ $doenca->doe_nome }}
                                        </span>
                                    @empty
                                        <span class="text-xs text-gray-500 italic">Nenhuma registrada</span>
                                    @endforelse
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Tabela de Doenças Identificadas --}}
    @if ($visitas->flatMap->doencas->isNotEmpty())
        <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow space-y-4">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Doenças Identificadas</h2>
            <p class="text-sm text-gray-600 dark:text-gray-400">
                Lista consolidada de doenças encontradas neste endereço, com sintomas, formas de transmissão e medidas de controle.
            </p>

            <div class="overflow-x-auto rounded-lg">
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
                        @foreach($visitas->flatMap->doencas->unique('doe_id') as $doenca)
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
    @endif

    {{-- Nova Consulta --}}
    <div class="text-center">
        <a href="{{ route('consulta.index') }}"
           class="inline-block mt-4 px-6 py-2 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg shadow transition">
            Nova Consulta
        </a>
    </div>
</div>

{{-- Map Script --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const lat = parseFloat("{{ $local->loc_latitude }}");
        const lng = parseFloat("{{ $local->loc_longitude }}");

        if (!isNaN(lat) && !isNaN(lng) && lat !== 0 && lng !== 0) {
            const mapa = L.map('mapa-local').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapa);
            L.marker([lat, lng]).addTo(mapa)
                .bindPopup("{{ $local->loc_endereco }}, {{ $local->loc_numero }}")
                .openPopup();
        } else {
            document.getElementById('mapa-local').innerHTML = '<div class="text-center text-sm text-gray-500 pt-16">Coordenadas indisponíveis para este local.</div>';
        }
    });
</script>
@endsection