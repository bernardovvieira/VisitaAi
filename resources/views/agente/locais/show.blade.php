<!-- resources/views/gestor/locais/show.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div>
        <a href="{{ route('agente.locais.index') }}"
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

<section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 space-y-8">
    <!-- Dados principais -->
    <dl class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">Código Único do Imóvel</dt>
            <dd class="mt-1">
                <span class="inline-block bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-semibold">
                    {{ $local->loc_codigo_unico }}
                </span>
            </dd>
        </div>

        <div>
            <dt class="font-medium">Tipo de Imóvel</dt>
            <dd class="mt-1">{{ $tipos[$local->loc_tipo] ?? 'Desconhecido' }}</dd>
        </div>

        <div>
            <dt class="font-medium">Quarteirão</dt>
            <dd class="mt-1">{{ $local->loc_quarteirao ?? 'N/A' }}</dd>
        </div>
        
        <div class="sm:col-span-3">
            <dt class="font-medium">Endereço Completo</dt>
            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif - {{ $local->loc_bairro }},
                {{ $local->loc_cidade }}/{{ $local->loc_estado }} - {{ $local->loc_pais }}, {{ $local->loc_cep }}
            </dd>
        </div>
    </dl>
    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
        <div>
            <dt class="font-medium">Latitude</dt>
            <dd class="mt-1">{{ $local->loc_latitude }}</dd>
        </div>
        <div>
            <dt class="font-medium">Longitude</dt>
            <dd class="mt-1">{{ $local->loc_longitude }}</dd>
        </div>
        <div>
            <dt class="font-medium">Criado em</dt>
            <dd class="mt-1">{{ $local->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        <div>
            <dt class="font-medium">Atualizado em</dt>
            <dd class="mt-1">{{ $local->updated_at->format('d/m/Y H:i') }}</dd>
        </div>
    </dl>

    <!-- Mapa -->
    <div>
        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Localização no Mapa</h3>
        <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
        <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
            A posição exibida é baseada nas coordenadas fornecidas pelo agente de saúde.
        </p>
    </div>

    <!-- Adesivo com QR Code -->
    <div class="pt-4 border-t border-gray-200 dark:border-gray-600 text-center">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">Adesivo para impressão</h3>

        <div id="adesivo" class="inline-block bg-white mt-3 text-gray-800 p-6 rounded-lg shadow-lg border border-gray-300 w-[320px]">
            <h3 class="text-lg font-bold mb-1">VISITA AÍ – CONSULTA PÚBLICA</h3>
            <p class="text-sm mb-2 leading-tight">
                {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif<br>
                {{ $local->loc_bairro }} – {{ $local->loc_cidade }}/{{ $local->loc_estado }}
            </p>
            <img src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="QR Code" class="mx-auto w-40 h-40 my-2">
            <p class="text-xs break-all text-center">
                {{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}
            </p>
        </div>
    </div>
    <div class="text-center">
        <button onclick="baixarAdesivo()"
            class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded shadow transition">
            Salvar Adesivo
        </button>
    </div>
    <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
        O adesivo pode ser impresso e colado no local para facilitar o acesso à consulta pública.
    </p>
</section>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
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

function baixarAdesivo() {
    const adesivo = document.getElementById('adesivo');

    html2canvas(adesivo).then(canvas => {
        const link = document.createElement('a');
        link.download = 'adesivo_qrcode_visita_ai_{{ $local->loc_codigo_unico }}.png';
        link.href = canvas.toDataURL('image/png');
        link.click();
    });
}
</script>
@endsection