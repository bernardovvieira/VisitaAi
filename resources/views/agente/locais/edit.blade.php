<!-- resources/views/agente/locais/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Locais', 'url' => route('agente.locais.index')], ['label' => 'Editar']]" />

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Local</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Preencha os dados do local. Preencha o CEP para preencher automaticamente os campos de endereço, bairro, cidade e estado.
            Utilize o botão "Minha Localização" para obter as coordenadas do dispositivo.
        </p>
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agente.locais.update', $local) }}" class="space-y-6" id="form_local">
            @csrf
            @method('PATCH')

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Características Principais</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo de Imóvel <span class="text-red-500">*</span></label>
                        <select id="loc_tipo" name="loc_tipo" required
                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                            <option value="" disabled>Selecione o tipo de imóvel</option>
                            <option value="R" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'R' ? 'selected' : '' }}>Residencial (R)</option>
                            <option value="C" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'C' ? 'selected' : '' }}>Comercial (C)</option>
                            <option value="T" {{ old('loc_tipo', $local->loc_tipo ?? '') == 'T' ? 'selected' : '' }}>Terreno Baldio (T)</option>
                        </select>
                    </div>
                    <div>
                        <label for="loc_zona" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Zona <span class="text-red-500">*</span></label>
                        <select id="loc_zona" name="loc_zona" required
                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
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
                    <div id="wrap_loc_cep">
                        <label for="loc_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP <span class="text-red-500">*</span></label>
                        <input id="loc_cep" name="loc_cep" type="text" maxlength="9" required value="{{ old('loc_cep', $local->loc_cep ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600 cep"
                            data-cep-permitido="{{ $cepPermitido ?? '' }}"
                            data-cidade-estado="{{ isset($cidadeEstado) ? json_encode($cidadeEstado) : '' }}">
                        <p id="loc_cep_erro" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" role="alert"></p>
                    </div>
                    <div>
                        <label for="loc_endereco" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Logradouro <span class="text-red-500">*</span></label>
                        <input id="loc_endereco" name="loc_endereco" type="text" required value="{{ old('loc_endereco', $local->loc_endereco ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número</label>
                        <input id="loc_numero" name="loc_numero" type="number" value="{{ old('loc_numero', $local->loc_numero ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bairro/Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_bairro" name="loc_bairro" type="text" required value="{{ old('loc_bairro', $local->loc_bairro ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_complemento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Complemento</label>
                        <input id="loc_complemento" name="loc_complemento" type="text" value="{{ old('loc_complemento', $local->loc_complemento ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Cidade <span class="text-red-500">*</span></label>
                        <input id="loc_cidade" name="loc_cidade" type="text" required readonly value="{{ old('loc_cidade', $local->loc_cidade ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_estado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Estado <span class="text-red-500">*</span></label>
                        <input id="loc_estado" name="loc_estado" type="text" required readonly value="{{ old('loc_estado', $local->loc_estado ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_pais" class="block text-sm font-medium text-gray-700 dark:text-gray-300">País <span class="text-red-500">*</span></label>
                        <input id="loc_pais" name="loc_pais" type="text" required readonly value="{{ old('loc_pais', $local->loc_pais ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Responsável pelo imóvel</legend>
                <div>
                    <label for="loc_responsavel_nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome completo (morador, locatário ou proprietário)</label>
                    <input id="loc_responsavel_nome" name="loc_responsavel_nome" type="text" value="{{ old('loc_responsavel_nome', $local->loc_responsavel_nome ?? '') }}" maxlength="255"
                           class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                           placeholder="Opcional">
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informações Complementares</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_codigo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código da Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_codigo" name="loc_codigo" type="number" required value="{{ old('loc_codigo', $local->loc_codigo ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_categoria" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Categoria da Localidade</label>
                        <input id="loc_categoria" name="loc_categoria" type="text" value="{{ old('loc_categoria', $local->loc_categoria ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_quarteirao" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quarteirão</label>
                        <input id="loc_quarteirao" name="loc_quarteirao" type="number" value="{{ old('loc_quarteirao', $local->loc_quarteirao ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_sequencia" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Sequência</label>
                        <input id="loc_sequencia" name="loc_sequencia" type="number" value="{{ old('loc_sequencia', $local->loc_sequencia ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_lado" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Lado</label>
                        <input id="loc_lado" name="loc_lado" type="number" value="{{ old('loc_lado', $local->loc_lado ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Geolocalização</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="loc_latitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Latitude <span class="text-red-500">*</span></label>
                        <input id="loc_latitude" name="loc_latitude" type="text" required value="{{ old('loc_latitude', $local->loc_latitude ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="loc_longitude" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Longitude <span class="text-red-500">*</span></label>
                        <input id="loc_longitude" name="loc_longitude" type="text" required value="{{ old('loc_longitude', $local->loc_longitude ?? '') }}"
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="btn-minha-localizacao" onclick="obterMinhaLocalizacao()"
                                class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg shadow transition">
                            Minha Localização
                        </button>
                    </div>
                </div>
                <div>
                    <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
                    <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                        Você pode ajustar a posição arrastando o marcador no mapa ou preenchendo latitude e longitude manualmente.
                    </p>
                    <p id="map-offline-aviso" class="hidden mt-2 text-sm text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/30 px-3 py-2 rounded border border-amber-200 dark:border-amber-800">
                        Como não há internet, será necessário ajustar o pin do mapa posteriormente com conexão para assegurar que tudo seja exato.
                    </p>
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Informações Adicionais</legend>
                <div>
                    <label for="loc_codigo_unico" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Código Único do Imóvel <span class="text-red-500">*</span></label>
                    <input id="loc_codigo_unico" name="loc_codigo_unico" type="number" value="{{ old('loc_codigo_unico', $local->loc_codigo_unico) }}" required readonly
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    @error('loc_codigo_unico')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                    <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                        O código único do imóvel é gerado automaticamente e não pode ser alterado. Ele é utilizado para identificar o local de forma exclusiva no sistema.
                    </p>
                </div>
            </fieldset>

            <div class="flex justify-end">
                <button type="submit"
                        class="btn-acesso-principal px-6 py-2 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Salvar Alterações
                </button>
            </div>
        </form>
    </section>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
<script>
var pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';

document.addEventListener('DOMContentLoaded', function() {
    var cepInput = document.getElementById('loc_cep');
    var cepPermitido = (cepInput && cepInput.getAttribute('data-cep-permitido')) || '';
    cepPermitido = (cepPermitido || '').trim();
    var cepPermitidoNorm = cepPermitido ? cepPermitido.replace(/\D/g, '') : '';
    var cidadeEstadoRaw = (cepInput && cepInput.getAttribute('data-cidade-estado')) || '';
    var cidadeEstado = cidadeEstadoRaw ? (function() { try { return JSON.parse(cidadeEstadoRaw); } catch(e) { return null; } })() : null;
    var cepsCadastrados = @json($cepsCadastrados ?? []);
    function normStr(s) { if (!s || typeof s !== 'string') return ''; return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/\s+/g,' ').trim(); }
    var cepValidouMunicipio = false;
    function getCepFromCadastrados(cepNorm) {
        if (!cepsCadastrados || !cepsCadastrados.length) return null;
        var key = String(cepNorm).replace(/\D/g, '').slice(0, 8);
        if (key.length !== 8) return null;
        for (var i = 0; i < cepsCadastrados.length; i++) { if (cepsCadastrados[i].cep === key) return cepsCadastrados[i]; }
        return null;
    }
    if (cidadeEstado && document.getElementById('loc_cidade') && document.getElementById('loc_estado')) {
        var curCidade = (document.getElementById('loc_cidade').value || '').trim();
        var curEstado = (document.getElementById('loc_estado').value || '').trim();
        if (normStr(curCidade) === normStr(cidadeEstado.cidade||'') && curEstado.toUpperCase() === (cidadeEstado.estado||'').toUpperCase()) cepValidouMunicipio = true;
    }
    function normCep(v) { return (v || '').replace(/\D/g, ''); }
    function checkCepLive() {
        var inp = document.getElementById('loc_cep');
        var msg = document.getElementById('loc_cep_erro');
        if (!inp || !msg) return true;
        var val = normCep(inp.value);
        if (val.length !== 8) { msg.classList.add('hidden'); inp.classList.remove('border-red-500', 'dark:border-red-400'); cepValidouMunicipio = false; return !cidadeEstado; }
        if (!cidadeEstado && !cepPermitidoNorm) { msg.classList.add('hidden'); inp.classList.remove('border-red-500', 'dark:border-red-400'); return true; }
        if (cepPermitidoNorm && val === cepPermitidoNorm) { msg.classList.add('hidden'); inp.classList.remove('border-red-500', 'dark:border-red-400'); return true; }
        if (cidadeEstado && cepValidouMunicipio) { msg.classList.add('hidden'); inp.classList.remove('border-red-500', 'dark:border-red-400'); return true; }
        if (cidadeEstado) { msg.textContent = 'O CEP deve pertencer ao município ' + (cidadeEstado.cidade || '') + '/' + (cidadeEstado.estado || '') + '. Preencha o CEP e aguarde a validação.'; msg.classList.remove('hidden'); inp.classList.add('border-red-500', 'dark:border-red-400'); return false; }
        if (cepPermitidoNorm) { msg.textContent = 'O sistema está vinculado a um único município. O CEP deve ser ' + (cepPermitido || '') + '.'; msg.classList.remove('hidden'); inp.classList.add('border-red-500', 'dark:border-red-400'); return false; }
        return true;
    }
    window._setCepValidouMunicipio = function(v) { cepValidouMunicipio = v; };
    var formEl = document.getElementById('form_local');
    if (formEl) formEl.addEventListener('submit', function(e) {
        if (!checkCepLive()) { e.preventDefault(); return false; }
    });

    $('#loc_cep').mask('00000-000');

    const lat = parseFloat("{{ $local->loc_latitude }}") || -28.7;
    const lng = parseFloat("{{ $local->loc_longitude }}") || -52.3;
    const map = L.map('map').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);

    var marker = L.marker([lat, lng], { draggable: true, icon: L.divIcon({ className: 'custom-pin', html: pinSvg, iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40] }) }).addTo(map);
    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        document.getElementById('loc_latitude').value = pos.lat.toFixed(7);
        document.getElementById('loc_longitude').value = pos.lng.toFixed(7);
        map.setView([pos.lat, pos.lng], 16);
    });

    window.setMapPosition = function(lat, lng) {
        var latN = parseFloat(lat), lngN = parseFloat(lng);
        if (isNaN(latN) || isNaN(lngN)) return;
        marker.setLatLng([latN, lngN]);
        map.setView([latN, lngN], 16);
        document.getElementById('loc_latitude').value = latN.toFixed(7);
        document.getElementById('loc_longitude').value = lngN.toFixed(7);
        setTimeout(function() { map.invalidateSize(); }, 100);
    };

    window.geocodeEndereco = function(callback) {
        var endereco = (document.getElementById('loc_endereco') && document.getElementById('loc_endereco').value) || '';
        var bairro = (document.getElementById('loc_bairro') && document.getElementById('loc_bairro').value) || '';
        var cidade = (document.getElementById('loc_cidade') && document.getElementById('loc_cidade').value) || '';
        var estado = (document.getElementById('loc_estado') && document.getElementById('loc_estado').value) || '';
        var pais = (document.getElementById('loc_pais') && document.getElementById('loc_pais').value) || '';
        if (!cidade || !estado) { if (callback) callback(false); return; }
        var q = [endereco, bairro, cidade, estado, pais || 'Brasil'].filter(Boolean).join(', ');
        if (!q) { if (callback) callback(false); return; }
        fetch('https://nominatim.openstreetmap.org/search?q=' + encodeURIComponent(q) + '&format=json&limit=1', {
            headers: { 'Accept': 'application/json', 'User-Agent': 'VisitaAi/1.0 (contato@bitwise.dev.br)' }
        }).then(function(r) { return r.json(); }).then(function(arr) {
            if (arr && arr[0] && typeof window.setMapPosition === 'function') {
                window.setMapPosition(parseFloat(arr[0].lat), parseFloat(arr[0].lon));
                if (callback) callback(true);
            } else { if (callback) callback(false); }
        }).catch(function() { if (callback) callback(false); });
    };

    function clearCepAddressFields() {
        var ids = ['loc_endereco', 'loc_bairro', 'loc_cidade', 'loc_estado', 'loc_pais'];
        ids.forEach(function(id) { var el = document.getElementById(id); if (el) el.value = ''; });
    }
    function applyCepData(data, msgEl, skipLogradouroBairro) {
        if (!data || data.erro) return false;
        var inp = document.getElementById('loc_cep');
        if (cidadeEstado) {
            var ok = normStr((data.localidade||'')) === normStr(cidadeEstado.cidade||'') && (data.uf||'').toUpperCase() === (cidadeEstado.estado||'').toUpperCase();
            window._setCepValidouMunicipio && window._setCepValidouMunicipio(ok);
            if (!ok) {
                if (msgEl) { msgEl.textContent = 'O CEP informado não pertence ao município ' + (cidadeEstado.cidade||'') + '/' + (cidadeEstado.estado||'') + '.'; msgEl.classList.remove('hidden'); }
                if (inp) inp.classList.add('border-red-500', 'dark:border-red-400');
                setTimeout(function() { if (cepInput) cepInput.focus(); }, 0);
                return false;
            }
        }
        if (msgEl) { msgEl.classList.add('hidden'); }
        if (inp) inp.classList.remove('border-red-500', 'dark:border-red-400');
        if (!skipLogradouroBairro) {
            var el = document.getElementById('loc_endereco'); if (el) el.value = data.logradouro || '';
            el = document.getElementById('loc_bairro'); if (el) el.value = data.bairro || '';
        }
        var el = document.getElementById('loc_cidade'); if (el) el.value = data.localidade || '';
        el = document.getElementById('loc_estado'); if (el) el.value = data.uf || '';
        el = document.getElementById('loc_pais'); if (el) el.value = 'Brasil';
        var cepNorm = normCep((document.getElementById('loc_cep') && document.getElementById('loc_cep').value) || '');
        if (data.latitude != null && data.longitude != null && !isNaN(parseFloat(data.latitude)) && !isNaN(parseFloat(data.longitude))) {
            if (typeof window.setMapPosition === 'function') window.setMapPosition(parseFloat(data.latitude), parseFloat(data.longitude));
        } else if (typeof window.geocodeEndereco === 'function') {
            window.geocodeEndereco(function(ok) {
                if (!ok && cepNorm.length === 8) {
                    var fromCad = getCepFromCadastrados(cepNorm);
                    if (fromCad && fromCad.latitude != null && fromCad.longitude != null && typeof window.setMapPosition === 'function') window.setMapPosition(parseFloat(fromCad.latitude), parseFloat(fromCad.longitude));
                }
            });
        }
        return true;
    }
    function isCepOffline() {
        if (typeof window.visitaConnectionOnline === 'boolean') return !window.visitaConnectionOnline;
        return !navigator.onLine;
    }
    if (cepInput) {
        cepInput.addEventListener('input', function() { checkCepLive(); });
        cepInput.addEventListener('blur', function() {
            var cep = normCep(cepInput.value);
            var msg = document.getElementById('loc_cep_erro');
            if (cep.length === 0) {
                clearCepAddressFields();
                if (msg) { msg.classList.add('hidden'); }
                cepInput.classList.remove('border-red-500', 'dark:border-red-400');
                window._setCepValidouMunicipio && window._setCepValidouMunicipio(false);
                return;
            }
            if (cep.length > 0 && cep.length < 8) {
                if (msg) { msg.textContent = 'Informe um CEP válido (8 dígitos) ou deixe em branco.'; msg.classList.remove('hidden'); }
                cepInput.classList.add('border-red-500', 'dark:border-red-400');
                setTimeout(function() { cepInput.focus(); }, 0);
                return;
            }
            if (cep.length !== 8) return;
            if (isCepOffline()) {
                var fromSistema = getCepFromCadastrados(cep);
                if (fromSistema && applyCepData(fromSistema, msg, true)) {
                    if (msg) { msg.textContent = 'Cidade/estado e posição do mapa preenchidos por local já cadastrado. Preencha endereço e bairro.'; msg.classList.remove('hidden'); msg.classList.remove('text-red-600', 'dark:text-red-400'); msg.classList.add('text-gray-600', 'dark:text-gray-400'); }
                    setTimeout(function() { if (msg) { msg.classList.add('hidden'); msg.classList.remove('text-gray-600', 'dark:text-gray-400'); msg.classList.add('text-red-600', 'dark:text-red-400'); } }, 3000);
                    return;
                }
                if (typeof window.VisitaOfflineGetCepCache !== 'function') {
                    if (msg) { msg.textContent = 'Sem conexão. Preencha o endereço manualmente.'; msg.classList.remove('hidden'); }
                    cepInput.classList.add('border-red-500', 'dark:border-red-400');
                    return;
                }
                window.VisitaOfflineGetCepCache(cep).then(function(data) {
                    if (data && applyCepData(data, msg)) {
                        if (msg) { msg.textContent = 'Endereço preenchido pelo cache (consulta anterior).'; msg.classList.remove('hidden'); msg.classList.remove('text-red-600', 'dark:text-red-400'); msg.classList.add('text-gray-600', 'dark:text-gray-400'); }
                        setTimeout(function() { if (msg) { msg.classList.add('hidden'); msg.classList.remove('text-gray-600', 'dark:text-gray-400'); msg.classList.add('text-red-600', 'dark:text-red-400'); } }, 3000);
                    } else {
                        if (msg) { msg.textContent = 'Sem conexão. Este CEP não está em cache. Preencha o endereço manualmente.'; msg.classList.remove('hidden'); }
                        cepInput.classList.add('border-red-500', 'dark:border-red-400');
                    }
                }).catch(function() {
                    if (msg) { msg.textContent = 'Sem conexão. Preencha o endereço manualmente.'; msg.classList.remove('hidden'); }
                    cepInput.classList.add('border-red-500', 'dark:border-red-400');
                });
                return;
            }
            var prev = window._viacepCallback;
            window._viacepCallback = function(data) {
                window._viacepCallback = prev;
                if (data && !data.erro) {
                    if (typeof window.VisitaOfflineSetCepCache === 'function') window.VisitaOfflineSetCepCache(cep, data);
                    applyCepData(data, msg);
                } else {
                    if (window._setCepValidouMunicipio) window._setCepValidouMunicipio(false);
                    if (msg) { msg.textContent = 'CEP não encontrado. Informe um CEP válido ou deixe em branco.'; msg.classList.remove('hidden'); }
                    cepInput.classList.add('border-red-500', 'dark:border-red-400');
                    setTimeout(function() { cepInput.focus(); }, 0);
                }
            };
            var script = document.createElement('script');
            script.src = 'https://viacep.com.br/ws/' + cep + '/json/?callback=_viacepCallback';
            script.onerror = function() {
                window._viacepCallback = prev;
                var fromSistema = getCepFromCadastrados(cep);
                if (fromSistema && applyCepData(fromSistema, msg, true)) return;
                if (typeof window.VisitaOfflineGetCepCache === 'function') {
                    window.VisitaOfflineGetCepCache(cep).then(function(cached) {
                        if (cached && applyCepData(cached, msg)) return;
                        if (msg) { msg.textContent = 'Erro ao buscar CEP. Verifique a conexão ou preencha manualmente.'; msg.classList.remove('hidden'); }
                        cepInput.classList.add('border-red-500', 'dark:border-red-400');
                        setTimeout(function() { cepInput.focus(); }, 0);
                    }).catch(function() {
                        if (msg) { msg.textContent = 'Erro ao buscar CEP. Verifique a conexão ou preencha manualmente.'; msg.classList.remove('hidden'); }
                        cepInput.classList.add('border-red-500', 'dark:border-red-400');
                        setTimeout(function() { cepInput.focus(); }, 0);
                    });
                } else {
                    if (msg) { msg.textContent = 'Erro ao buscar CEP. Verifique a conexão ou deixe em branco.'; msg.classList.remove('hidden'); }
                    cepInput.classList.add('border-red-500', 'dark:border-red-400');
                    setTimeout(function() { cepInput.focus(); }, 0);
                }
            };
            document.body.appendChild(script);
        });
    }
    var mapOfflineAviso = document.getElementById('map-offline-aviso');
    if (mapOfflineAviso) {
        function updateMapOfflineAviso() {
            var off = typeof window.visitaConnectionOnline === 'boolean' ? !window.visitaConnectionOnline : !navigator.onLine;
            if (off) mapOfflineAviso.classList.remove('hidden'); else mapOfflineAviso.classList.add('hidden');
        }
        updateMapOfflineAviso();
        document.addEventListener('visita-connection-change', updateMapOfflineAviso);
        window.addEventListener('visita-connection-change', updateMapOfflineAviso);
    }
});

function obterMinhaLocalizacao() {
    var btn = document.getElementById('btn-minha-localizacao');
    if (btn) { btn.disabled = true; btn.textContent = 'Obtendo...'; }
    if (!navigator.geolocation) {
        alert('Geolocalização não é suportada por este navegador. Arraste o marcador no mapa ou preencha latitude e longitude manualmente.');
        if (btn) { btn.disabled = false; btn.textContent = 'Minha Localização'; }
        return;
    }
    function onOk(pos) {
        if (typeof window.setMapPosition === 'function') window.setMapPosition(pos.coords.latitude, pos.coords.longitude);
        if (btn) { btn.disabled = false; btn.textContent = 'Minha Localização'; }
    }
    function onErr(err, tentarSemPrecisao) {
        if (err.code === 1 && tentarSemPrecisao) {
            navigator.geolocation.getCurrentPosition(onOk, function(e2) { onErr(e2, false); }, { enableHighAccuracy: false, timeout: 10000, maximumAge: 300000 });
            return;
        }
        if (btn) { btn.disabled = false; btn.textContent = 'Minha Localização'; }
        if (typeof window.geocodeEndereco === 'function') {
            window.geocodeEndereco(function(ok) {
                if (ok) alert('Não foi possível usar sua localização. O marcador foi posicionado no endereço informado (CEP).');
                else alert('Não foi possível usar a localização do dispositivo. Informe um CEP (para preencher endereço) ou arraste o marcador no mapa / digite latitude e longitude manualmente.');
            });
        } else {
            alert('Não foi possível usar a localização do dispositivo. Arraste o marcador no mapa ou digite latitude e longitude manualmente.');
        }
    }
    navigator.geolocation.getCurrentPosition(onOk, function(err) { onErr(err, true); }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 });
}
</script>
@endsection