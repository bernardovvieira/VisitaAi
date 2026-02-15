<!-- resources/views/agente/locais/edit.blade.php -->
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

        <form method="POST" action="{{ route('agente.locais.update', $local) }}" class="space-y-6" id="form_local">
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
                    <div id="wrap_loc_cep">
                        <label for="loc_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">CEP <span class="text-red-500">*</span></label>
                        <input id="loc_cep" name="loc_cep" type="text" maxlength="9" required value="{{ old('loc_cep', $local->loc_cep ?? '') }}"
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm cep"
                            data-cep-permitido="{{ $cepPermitido ?? '' }}"
                            data-cidade-estado="{{ isset($cidadeEstado) ? json_encode($cidadeEstado) : '' }}">
                        <p id="loc_cep_erro" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" role="alert"></p>
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
                        <button type="button" id="btn-minha-localizacao" onclick="obterMinhaLocalizacao()"
                                class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow transition">
                            Minha Localização
                        </button>
                    </div>
                </div>
                <div>
                    <div id="map" class="h-72 rounded-md shadow border border-gray-300"></div>
                    <p class="text-sm mt-2 text-gray-600 dark:text-gray-400 italic">
                        Você pode ajustar a posição arrastando o marcador no mapa ou preenchendo latitude e longitude manualmente.
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
    function normStr(s) { if (!s || typeof s !== 'string') return ''; return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/\s+/g,' ').trim(); }
    var cepValidouMunicipio = false;
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
            var prev = window._viacepCallback;
            window._viacepCallback = function(data) {
                window._viacepCallback = prev;
                if (data && !data.erro) {
                    var inp = document.getElementById('loc_cep');
                    var msgEl = document.getElementById('loc_cep_erro');
                    if (cidadeEstado) {
                        var ok = normStr((data.localidade||'')) === normStr(cidadeEstado.cidade||'') && (data.uf||'').toUpperCase() === (cidadeEstado.estado||'').toUpperCase();
                        window._setCepValidouMunicipio && window._setCepValidouMunicipio(ok);
                        if (ok) { msgEl.classList.add('hidden'); inp.classList.remove('border-red-500', 'dark:border-red-400'); }
                        else { msgEl.textContent = 'O CEP informado não pertence ao município ' + (cidadeEstado.cidade||'') + '/' + (cidadeEstado.estado||'') + '.'; msgEl.classList.remove('hidden'); inp.classList.add('border-red-500', 'dark:border-red-400'); setTimeout(function() { cepInput.focus(); }, 0); return; }
                    }
                    var el = document.getElementById('loc_endereco'); if (el) el.value = data.logradouro || '';
                    el = document.getElementById('loc_bairro'); if (el) el.value = data.bairro || '';
                    el = document.getElementById('loc_cidade'); if (el) el.value = data.localidade || '';
                    el = document.getElementById('loc_estado'); if (el) el.value = data.uf || '';
                    el = document.getElementById('loc_pais'); if (el) el.value = 'Brasil';
                    window.geocodeEndereco(function(ok) {});
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
                if (msg) { msg.textContent = 'Erro ao buscar CEP. Verifique a conexão ou deixe em branco.'; msg.classList.remove('hidden'); }
                cepInput.classList.add('border-red-500', 'dark:border-red-400');
                setTimeout(function() { cepInput.focus(); }, 0);
            };
            document.body.appendChild(script);
        });
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