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

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Características Principais</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Imóvel <span class="text-red-500">*</span></label>
                        <select id="loc_tipo" name="loc_tipo" required
                                class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                            <option value="" disabled>Selecione o tipo de imóvel</option>
                            <option value="R" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'R' ? 'selected' : '' }}>Residencial (R)</option>
                            <option value="C" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'C' ? 'selected' : '' }}>Comercial (C)</option>
                            <option value="T" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'T' ? 'selected' : '' }}>Terreno Baldio (T)</option>
                        </select>
                    </div>
                    <div>
                        <label for="loc_zona" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zona <span class="text-red-500">*</span></label>
                        <select id="loc_zona" name="loc_zona" required
                                class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                            <option value="" disabled>Selecione a zona</option>
                            <option value="U" {{ old('loc_zona', $local->loc_zona ?? '') == 'U' ? 'selected' : '' }}>Urbana (U)</option>
                            <option value="R" {{ old('loc_zona', $local->loc_zona ?? '') == 'R' ? 'selected' : '' }}>Rural (R)</option>
                        </select>
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Endereço Completo</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP <span class="text-red-500">*</span></label>
                        <input id="loc_cep" name="loc_cep" type="text" maxlength="9" required value="{{ old('loc_cep', $local->loc_cep ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm cep">
                    </div>
                    <div>
                        <label for="loc_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro <span class="text-red-500">*</span></label>
                        <input id="loc_endereco" name="loc_endereco" type="text" required value="{{ old('loc_endereco', $local->loc_endereco ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                        <input id="loc_numero" name="loc_numero" type="number" value="{{ old('loc_numero', $local->loc_numero ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro/Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_bairro" name="loc_bairro" type="text" required value="{{ old('loc_bairro', $local->loc_bairro ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_complemento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complemento</label>
                        <input id="loc_complemento" name="loc_complemento" type="text" value="{{ old('loc_complemento', $local->loc_complemento ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade <span class="text-red-500">*</span></label>
                        <input id="loc_cidade" name="loc_cidade" type="text" required readonly value="{{ old('loc_cidade', $local->loc_cidade ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado <span class="text-red-500">*</span></label>
                        <input id="loc_estado" name="loc_estado" type="text" required readonly value="{{ old('loc_estado', $local->loc_estado ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_pais" class="block text-sm font-medium text-gray-700 dark:text-gray-300">País <span class="text-red-500">*</span></label>
                        <input id="loc_pais" name="loc_pais" type="text" required readonly value="{{ old('loc_pais', $local->loc_pais ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informações Complementares</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código da Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_codigo" name="loc_codigo" type="number" required value="{{ old('loc_codigo', $local->loc_codigo ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria da Localidade</label>
                        <input id="loc_categoria" name="loc_categoria" type="text" value="{{ old('loc_categoria', $local->loc_categoria ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_quarteirao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quarteirão</label>
                        <input id="loc_quarteirao" name="loc_quarteirao" type="number" value="{{ old('loc_quarteirao', $local->loc_quarteirao ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_sequencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sequência</label>
                        <input id="loc_sequencia" name="loc_sequencia" type="number" value="{{ old('loc_sequencia', $local->loc_sequencia ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_lado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lado</label>
                        <input id="loc_lado" name="loc_lado" type="number" value="{{ old('loc_lado', $local->loc_lado ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Geolocalização</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="loc_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude <span class="text-red-500">*</span></label>
                        <input id="loc_latitude" name="loc_latitude" type="text" required value="{{ old('loc_latitude', $local->loc_latitude ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="loc_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude <span class="text-red-500">*</span></label>
                        <input id="loc_longitude" name="loc_longitude" type="text" required value="{{ old('loc_longitude', $local->loc_longitude ?? '') }}"
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
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informações Adicionais</legend>
                <div>
                    <label for="loc_codigo_unico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Único do Imóvel <span class="text-red-500">*</span></label>
                    <input id="loc_codigo_unico" name="loc_codigo_unico" type="number" value="{{ old('loc_codigo_unico', $local->loc_codigo_unico) }}" required readonly
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    @error('loc_codigo_unico')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                        O código único do imóvel é gerado automaticamente e não pode ser alterado. Ele é utilizado para identificar o local de forma exclusiva no sistema.
                    </p>
                </div>
            </fieldset>

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