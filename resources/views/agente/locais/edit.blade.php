<!-- resources/views/agente/locais/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div>
        <a href="{{ route('agente.locais.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Local</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize os dados do local. Use o CEP para buscar endereço, ou utilize sua localização atual para preencher automaticamente. Você também pode ajustar o marcador no mapa.
        </p>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        <form method="POST" action="{{ route('agente.locais.update', $local) }}">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP</label>
                    <input id="cep" name="cep" type="text" maxlength="9" placeholder="00000-000" value="{{ old('cep') }}"
                           class="cep mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro <span class="text-red-500">*</span></label>
                    <input id="loc_endereco" name="loc_endereco" type="text" value="{{ old('loc_endereco', $local->loc_endereco) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número <span class="text-red-500">*</span></label>
                    <input id="numero" name="numero" type="text" value="{{ old('numero', $local->numero) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro <span class="text-red-500">*</span></label>
                    <input id="loc_bairro" name="loc_bairro" type="text" value="{{ old('loc_bairro', $local->loc_bairro) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade</label>
                    <input id="cidade" name="cidade" type="text" value="{{ old('cidade', $local->cidade) }}"
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado</label>
                    <input id="estado" name="estado" type="text" value="{{ old('estado', $local->estado) }}"
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="pais" class="block text-sm font-medium text-gray-700 dark:text-gray-300">País</label>
                    <input id="pais" name="pais" type="text" value="{{ old('pais', $local->pais ?? 'Brasil') }}" readonly
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude <span class="text-red-500">*</span></label>
                    <input id="latitude" name="latitude" type="text" value="{{ old('latitude', $local->latitude) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude <span class="text-red-500">*</span></label>
                    <input id="longitude" name="longitude" type="text" value="{{ old('longitude', $local->longitude) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div class="flex justify-end">
                    <button type="button" onclick="obterMinhaLocalizacao()"
                            class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                        Minha Localização
                    </button>
                </div>
            </div>

            <div>
                <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
                <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                    Você pode ajustar manualmente a posição do marcador no mapa para maior precisão.
                </p>
            </div>

            <div>
                <label for="loc_codigo_unico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Único do Imóvel <span class="text-red-500">*</span></label>
                <input id="loc_codigo_unico" name="loc_codigo_unico" type="text" value="{{ old('loc_codigo_unico', $local->loc_codigo_unico) }}" required
                       class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                @error('loc_codigo_unico')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </section>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    $('#cep').mask('00000-000');
});

let map = L.map('map').setView([
    parseFloat(document.getElementById('latitude').value) || -28.7,
    parseFloat(document.getElementById('longitude').value) || -52.3
], 13);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap'
}).addTo(map);

let marker = L.marker([
    parseFloat(document.getElementById('latitude').value) || -28.7,
    parseFloat(document.getElementById('longitude').value) || -52.3
], { draggable: true }).addTo(map);

marker.on('dragend', function(e) {
    const pos = e.target.getLatLng();
    document.getElementById('latitude').value = pos.lat.toFixed(7);
    document.getElementById('longitude').value = pos.lng.toFixed(7);
});

function setMapPosition(lat, lng) {
    marker.setLatLng([lat, lng]);
    map.setView([lat, lng], 16);
    document.getElementById('latitude').value = lat;
    document.getElementById('longitude').value = lng;
}

function obterMinhaLocalizacao() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(pos) {
            const lat = pos.coords.latitude.toFixed(7);
            const lng = pos.coords.longitude.toFixed(7);
            setMapPosition(lat, lng);
        }, function(error) {
            alert('Erro ao obter localização: ' + error.message);
        }, {
            enableHighAccuracy: true,
            timeout: 20000,
            maximumAge: 0
        });
    } else {
        alert('Geolocalização não suportada.');
    }
}

const cepInput = document.getElementById('cep');
cepInput.addEventListener('blur', () => {
    let cep = cepInput.value.replace(/\D/g, '');
    if (cep.length === 8) {
        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(res => res.json())
            .then(data => {
                if (!data.erro) {
                    document.getElementById('loc_endereco').value = data.logradouro || '';
                    document.getElementById('loc_bairro').value = data.bairro || '';
                    document.getElementById('cidade').value = data.localidade || '';
                    document.getElementById('estado').value = data.uf || '';
                    document.getElementById('pais').value = 'Brasil';
                } else {
                    alert('CEP não encontrado.');
                }
            });
    }
});
</script>

@endsection