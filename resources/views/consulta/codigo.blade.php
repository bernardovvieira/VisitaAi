@extends('layouts.app')

@section('public')
@endsection

@section('content')
<div class="max-w-4xl space-y-10">

    {{-- Cabeçalho --}}
    <div class="flex items-center justify-between" style="padding-top: 2rem;">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Resultado da Consulta</h1>
        <a href="{{ route('consulta.index') }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline font-medium flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar à Consulta
        </a>
    </div>

    {{-- Endereço e mapa --}}
    <section class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div class="space-y-2 text-sm text-gray-800 dark:text-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Imóvel Consultado</h2>
            <p><strong>Zona:</strong> {{ $local->loc_zona === 'U' ? 'Urbana' : ($local->loc_zona === 'R' ? 'Rural' : 'N/A') }}</p>
            <p><strong>Tipo de Imóvel:</strong> {{ $local->loc_tipo === 'R' ? 'Residencial' : ($local->loc_tipo === 'C' ? 'Comercial' : 'Terreno Baldio') }}</p>
            <p><strong>Quarteirão:</strong> {{ $local->loc_quarteirao ?? 'N/A' }}</p>
            <p><strong>Endereço:</strong> {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif</p>
            <p><strong>Bairro:</strong> {{ $local->loc_bairro }}</p>
            <p><strong>Cidade:</strong> {{ $local->loc_cidade }}/{{ $local->loc_estado }}</p>
            <p><strong>Código de Identificação:</strong> {{ $local->loc_codigo_unico }}</p>
        </div>
        <div class="w-full h-64 rounded-lg overflow-hidden border" id="mapa-local"></div>
    </section>

    {{-- Histórico de Visitas (sem doenças) --}}
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
                            <th class="px-4 py-3 text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                        @foreach ($visitas as $visita)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</td>
                                <td class="px-4 py-3">
                                    @if ($visita->vis_pendencias)
                                        @php
                                            $revisitaPosterior = $visita->local->visitas()
                                                ->where('vis_data', '>', $visita->vis_data)
                                                ->orderBy('vis_data')
                                                ->first();
                                        @endphp

                                        <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded dark:bg-red-900 dark:text-red-300">
                                            Pendente
                                        </span>

                                        @if($revisitaPosterior)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                                Revisitado em {{ \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">
                                            Concluída
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Informativo institucional --}}
    <section class="bg-yellow-50 dark:bg-yellow-900 border border-yellow-300 dark:border-yellow-700 text-yellow-800 dark:text-yellow-100 p-6 rounded-lg shadow space-y-2 text-sm">
        <h2 class="text-base font-semibold">Precisa de mais informações?</h2>
        <p>
            Esta consulta pública tem caráter informativo e exibe apenas o histórico de visitas realizadas pelos agentes no endereço informado.
        </p>
        <p>
            Para esclarecimentos adicionais sobre a situação epidemiológica do imóvel, entre em contato diretamente com a <strong>Secretaria Municipal de Saúde</strong>.
        </p>
    </section>

    {{-- Nova consulta --}}
    <div class="text-center">
        <a href="{{ route('consulta.index') }}"
           class="inline-flex items-center justify-center gap-2 mt-6 px-6 py-2.5 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Fazer nova consulta
        </a>
    </div>

</div>

{{-- Mapa --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@php
    $numero = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : 'N/A';
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('mapa-local');
        var msg = function(html) { el.innerHTML = '<div class="flex items-center justify-center h-full text-center text-sm text-gray-500 dark:text-gray-400 px-4">' + html + '</div>'; };
        try {
            if (typeof L === 'undefined') {
                msg('Mapa indisponível (recarregue a página ou verifique sua conexão).');
                return;
            }
            var lat = parseFloat("{{ $local->loc_latitude ?? '' }}");
            var lng = parseFloat("{{ $local->loc_longitude ?? '' }}");
            if (isNaN(lat) || isNaN(lng) || lat === 0 && lng === 0) {
                msg('Coordenadas indisponíveis para este local.');
                return;
            }
            var mapa = L.map('mapa-local').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapa);
            var pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
            var pinIcon = L.divIcon({
                className: 'custom-pin',
                html: pinSvg,
                iconSize: [28, 40],
                iconAnchor: [14, 40],
                popupAnchor: [0, -40]
            });
            L.marker([lat, lng], { icon: pinIcon }).addTo(mapa)
                .bindPopup("{{ addslashes($local->loc_endereco) }}, {{ addslashes($numero) }}")
                .openPopup();
            setTimeout(function() { mapa.invalidateSize(); }, 100);
        } catch (e) {
            msg('Não foi possível carregar o mapa.');
        }
    });
</script>

@endsection