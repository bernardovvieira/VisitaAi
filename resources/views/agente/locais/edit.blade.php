<!-- resources/views/agente/locais/edit.blade.php -->
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
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Local</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Preencha os dados do local. Preencha o CEP para preencher automaticamente os campos de endereço, bairro, cidade e estado.
            Utilize o botão "Minha Localização" para obter as coordenadas do dispositivo.
        </p>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agente.locais.update', $local) }}" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="loc_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP <span class="text-red-500">*</span></label>
                    <input id="loc_cep" name="loc_cep" type="text" value="{{ old('loc_cep', $local->loc_cep) }}" required maxlength="9"
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro <span class="text-red-500">*</span></label>
                    <input id="loc_endereco" name="loc_endereco" type="text" value="{{ old('loc_endereco', $local->loc_endereco) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="loc_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número <span class="text-red-500">*</span></label>
                    <input id="loc_numero" name="loc_numero" type="number" value="{{ old('loc_numero', $local->loc_numero) }}" required
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
                    <label for="loc_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade <span class="text-red-500">*</span></label>
                    <input id="loc_cidade" name="loc_cidade" type="text" value="{{ old('loc_cidade', $local->loc_cidade) }}" required readonly
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado <span class="text-red-500">*</span></label>
                    <input id="loc_estado" name="loc_estado" type="text" value="{{ old('loc_estado', $local->loc_estado) }}" required readonly
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_pais" class="block text-sm font-medium text-gray-700 dark:text-gray-300">País <span class="text-red-500">*</span></label>
                    <input id="loc_pais" name="loc_pais" type="text" value="{{ old('loc_pais', $local->loc_pais) }}" required readonly
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </div>

            <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                Os campos <strong>cidade</strong>, <strong>estado</strong> e <strong>país</strong> serão preenchidos automaticamente após digitar um CEP válido.
            </p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <div>
                    <label for="loc_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude <span class="text-red-500">*</span></label>
                    <input id="loc_latitude" name="loc_latitude" type="text" value="{{ old('loc_latitude', $local->loc_latitude) }}" required
                           class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <div>
                    <label for="loc_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude <span class="text-red-500">*</span></label>
                    <input id="loc_longitude" name="loc_longitude" type="text" value="{{ old('loc_longitude', $local->loc_longitude) }}" required
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
                <label for="loc_codigo_unico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Único do Imóvel<span class="text-red-500">*</span></label>
                <input id="loc_codigo_unico" name="loc_codigo_unico" type="number" value="{{ old('loc_codigo_unico', $local->loc_codigo_unico) }}" required
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
    $('#loc_cep').mask('00000-000');

    const lat = parseFloat("{{ $local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([lat, lng], { draggable: true }).addTo(map);
    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        document.getElementById('loc_latitude').value = pos.lat.toFixed(7);
        document.getElementById('loc_longitude').value = pos.lng.toFixed(7);
        map.setView([pos.lat, pos.lng], 16);
    });

    window.setMapPosition = function(lat, lng) {
        marker.setLatLng([lat, lng]);
        map.setView([lat, lng], 16);
        document.getElementById('loc_latitude').value = lat;
        document.getElementById('loc_longitude').value = lng;
    }

    document.getElementById('loc_cep').addEventListener('blur', () => {
        let cep = document.getElementById('loc_cep').value.replace(/\D/g, '');
        if (cep.length === 8) {
            fetch(`https://viacep.com.br/ws/${cep}/json/`)
                .then(res => res.json())
                .then(data => {
                    if (!data.erro) {
                        document.getElementById('loc_endereco').value = data.logradouro || '';
                        document.getElementById('loc_bairro').value = data.bairro || '';
                        document.getElementById('loc_cidade').value = data.localidade || '';
                        document.getElementById('loc_estado').value = data.uf || '';
                        document.getElementById('loc_pais').value = 'Brasil';
                    } else {
                        alert('CEP não encontrado.');
                    }
                });
        }
    });
});

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
</script>
@endsection