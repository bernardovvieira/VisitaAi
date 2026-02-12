@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
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
            Detalhes do local selecionado, incluindo informações de localização, tipo e categoria.
        </p>
    </section>

    <section class="bg-white dark:bg-gray-700 rounded-lg shadow p-6 space-y-8">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Informações do Local</h2>
        <dl class="grid grid-cols-1 sm:grid-cols-4 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300">
            <div>
                <dt class="font-medium">Código Único do Imóvel</dt>
                <dd class="mt-1">
                    <span class="inline-block bg-purple-100 text-purple-800 dark:bg-purple-800 dark:text-purple-200 px-2 py-1 rounded text-xs font-semibold">
                        {{ $local->loc_codigo_unico }}
                    </span>
                </dd>
            </div>
            <div>
                <dt class="font-medium">Código da Localidade</dt>
                <dd class="mt-1">{{ $local->loc_codigo ?? 'N/A' }}</dd>
            </div> 
            <div>
                <dt class="font-medium">Categoria da Localidade</dt>
                <dd class="mt-1">{{ $local->loc_categoria ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Tipo</dt>
                <dd class="mt-1">{{ ['R' => 'Residencial', 'C' => 'Comercial', 'T' => 'Terreno Baldio'][$local->loc_tipo] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Zona</dt>
                <dd class="mt-1">{{ ['U' => 'Urbana', 'R' => 'Rural'][$local->loc_zona] ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Quarteirão</dt>
                <dd class="mt-1">{{ $local->loc_quarteirao ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Sequência</dt>
                <dd class="mt-1">{{ $local->loc_sequencia ?? 'N/A' }}</dd>
            </div>
            <div>
                <dt class="font-medium">Lado</dt>
                <dd class="mt-1">{{ $local->loc_lado ?? 'N/A' }}</dd>
            </div>
            <div class="sm:col-span-4" style="padding-top: 0.5rem;">
                <dt class="font-medium">Endereço Completo</dt>
                <dd class="mt-1 text-gray-900 dark:text-gray-100">
                    {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }} - {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }} - {{ $local->loc_pais }} | CEP: {{ $local->loc_cep }}<br>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Complemento: {{ $local->loc_complemento ?? 'N/A' }}</span>
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

        <div>
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-2">Localização no Mapa</h3>
            <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
            <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                A posição exibida é baseada nas coordenadas fornecidas.
            </p>
        </div>
    </section>

    {{-- Adesivo --}}
    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-4 mt-10">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 border-b pb-2">Adesivo para Consulta Pública</h2>
        <div class="flex justify-center" style="padding-top: 2rem;">
            <div id="adesivo" class="bg-white text-gray-800 p-6 rounded-lg shadow-lg border border-gray-300 w-[320px] relative">
                <h3 class="text-lg font-bold mb-1 text-center">VISITA AÍ – CONSULTA PÚBLICA</h3>
                <p class="text-sm mb-2 leading-tight text-center">
                    {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}<br>
                    {{ $local->loc_bairro }} – {{ $local->loc_cidade }}/{{ $local->loc_estado }}
                </p>
                <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="QR Code" class="mx-auto w-40 h-40 my-2">
                <p class="text-xs break-all text-center">
                    {{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}
                </p>
                <p class="text-[10px] text-gray-400 text-center mt-3 pt-2 border-t border-gray-200">Desenvolvido por Bitwise Technologies</p>
            </div>
        </div>

        <div class="text-center" style="padding-top: 0.5rem;">
            <button onclick="baixarAdesivo()"
                    class="mt-4 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold rounded shadow transition">
                    <svg class="w-4 h-4 mr-2 inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                    </svg>
                Salvar Adesivo
            </button>
            <p class="mt-3 text-sm text-gray-600 dark:text-gray-400" style="padding-top: 0.5rem;">
                O adesivo pode ser impresso e colado no local para facilitar o acesso à consulta pública.
            </p>
        </div>
    </section>

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@php $numero = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : 'S/N'; @endphp
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('map');
    const msg = (html) => { el.innerHTML = '<div class="flex items-center justify-center h-full text-center text-sm text-gray-500 dark:text-gray-400 px-4">' + html + '</div>'; };
    try {
        if (typeof L === 'undefined') { msg('Mapa indisponível (recarregue a página ou verifique sua conexão).'); return; }
        const lat = parseFloat("{{ $local->loc_latitude ?? '' }}");
        const lng = parseFloat("{{ $local->loc_longitude ?? '' }}");
        if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) { msg('Coordenadas indisponíveis para este local.'); return; }
        const map = L.map('map').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        const pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
        const pinIcon = L.divIcon({ className: 'custom-pin', html: pinSvg, iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40] });
        L.marker([lat, lng], { icon: pinIcon }).addTo(map)
            .bindPopup("{{ addslashes($local->loc_endereco) }}, {{ addslashes($numero) }}")
            .openPopup();
        setTimeout(() => map.invalidateSize(), 100);
    } catch (e) { msg('Não foi possível carregar o mapa.'); }
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