<!-- resources/views/gestor/locais/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div>
        <a href="{{ route('gestor.locais.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
            Voltar
        </a>
    </div>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Detalhes do Local</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Informações completas do local registrado no sistema.
        </p>
    </section>

    <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 space-y-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Código Único do Imóvel</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                    <span class="inline-block bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-semibold">
                        {{ $local->loc_codigo_unico }}
                    </span>
                </dd>
            </div>
            <div class="sm:col-span-2">
                <dt class="font-medium">Endereço Completo</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $local->loc_endereco }}, {{ $local->loc_numero }} - {{ $local->loc_bairro }},
                    {{ $local->loc_cidade }}/{{ $local->loc_estado }} - {{ $local->loc_pais }}, {{ $local->loc_cep }}
                </dd>
            </div>
            <div>
                <dt class="font-medium">Latitude</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_latitude }}</dd>
            </div>
            <div>
                <dt class="font-medium">Longitude</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_longitude }}</dd>
            </div>
            <div>
                <dt class="font-medium">Criado em</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="font-medium">Atualizado em</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->updated_at->format('d/m/Y H:i') }}</dd>
            </div>
        </dl>

        <div class="pt-4">
            <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
            <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                Lembre-se que a localização exibida no mapa é baseada nas coordenadas fornecidas pelo agente.
            </p>
        </div>
    </section>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const lat = parseFloat("{{ $local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    L.marker([lat, lng]).addTo(map);
});
</script>
@endsection