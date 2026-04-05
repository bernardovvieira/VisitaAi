<!-- resources/views/agente/locais/create.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . (($isPrimario ?? false) ? __('Cadastrar local de referência') : __('Cadastrar local')))
@section('og_description', __('Cadastro de imóvel para visitas de vigilância entomológica e controle vetorial.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais'), 'url' => route('agente.locais.index')], ['label' => __('Cadastrar')]]" />

    @if($isPrimario ?? false)
    <x-section-card class="v-card--muted">
        <h2 class="text-base font-semibold text-gray-800 dark:text-gray-200 flex items-center gap-2">
            <x-heroicon-o-information-circle class="h-4 w-4 shrink-0 text-gray-500 dark:text-gray-400" />
            {{ __('Local de referência do município') }}
        </h2>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Este é o primeiro local e define cidade/estado do sistema. Sugerimos a prefeitura ou a secretaria de saúde. Não poderá ser editado nem excluído pela interface; para alterações, entre em contato com o suporte.') }}
        </p>
    </x-section-card>
    @endif

    <x-page-header :eyebrow="__('Locais')" :title="($isPrimario ?? false) ? __('Cadastrar local de referência') : __('Cadastrar local')" />

    <x-ui.disclosure variant="muted-card">
        <x-slot name="summary">
            <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ __('Modo offline, CEP e localização (expandir)') }}</span>
        </x-slot>
        <p><strong>{{ __('Sem internet?') }}</strong> {{ __('Use o botão «Guardar local» para salvar no dispositivo e sincronize depois na aba Sincronizar.') }}</p>
        <p><strong>{{ __('Nota:') }}</strong> {{ __('Antes de ir a campo, abra esta tela pelo menos uma vez com internet para o sistema guardar a página e funcionar offline.') }}</p>
        <p>{{ __('Preencha o CEP para completar endereço automaticamente. Use «Minha localização» para coordenadas do dispositivo.') }}</p>
    </x-ui.disclosure>

    <x-section-card class="space-y-4">
        <x-flash-alerts />

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route($storeRoute ?? 'agente.locais.store') }}" class="space-y-6" id="form_local"
            x-data="{ carregando: false }"
            x-on:submit="carregando = true">

            @csrf

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Características Principais</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_tipo" class="v-toolbar-label">Tipo de Imóvel <span class="text-red-500">*</span></label>
                        <select id="loc_tipo" name="loc_tipo" required
                                class="v-select mt-1">
                            <option value="" disabled selected>Selecione o tipo de imóvel</option>
                            <option value="R" {{ old('loc_tipo') == 'R' ? 'selected' : '' }}>Residencial (R)</option>
                            <option value="C" {{ old('loc_tipo') == 'C' ? 'selected' : '' }}>Comercial (C)</option>
                            <option value="T" {{ old('loc_tipo') == 'T' ? 'selected' : '' }}>Terreno Baldio (T)</option>
                        </select>
                    </div>
                    <div>
                        <label for="loc_zona" class="v-toolbar-label">Zona <span class="text-red-500">*</span></label>
                        <select id="loc_zona" name="loc_zona" required
                                class="v-select mt-1">
                            <option value="" disabled selected>Selecione a zona</option>
                            <option value="U" {{ old('loc_zona') == 'U' ? 'selected' : '' }}>Urbana (U)</option>
                            <option value="R" {{ old('loc_zona') == 'R' ? 'selected' : '' }}>Rural (R)</option>
                        </select>
                    </div>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                    Os campos acima serão utilizados em relatórios e análises de localização, certifique-se de escolher as opções corretas.
            </fieldset> 

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Endereço Completo</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div id="wrap_loc_cep">
                        <label for="cep" class="v-toolbar-label">CEP <span class="text-red-500">*</span></label>
                        <input id="loc_cep" name="loc_cep" type="text" maxlength="9" placeholder="00000-000" required
                            class="cep v-input mt-1"
                            data-cep-permitido="{{ $cepPermitido ?? '' }}"
                            data-cidade-estado="{{ isset($cidadeEstado) ? json_encode($cidadeEstado) : '' }}">
                        <p id="loc_cep_erro" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" role="alert"></p>
                    </div>
                    <div>
                        <label for="loc_endereco" class="v-toolbar-label">Logradouro <span class="text-red-500">*</span></label>
                        <input id="loc_endereco" name="loc_endereco" type="text" value="{{ old('loc_endereco') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="numero" class="v-toolbar-label">Número</label>
                        <input id="loc_numero" name="loc_numero" type="number"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_bairro" class="v-toolbar-label">Bairro/Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_bairro" name="loc_bairro" type="text" value="{{ old('loc_bairro') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="loc_complemento" class="v-toolbar-label">Complemento</label>
                        <input id="loc_complemento" name="loc_complemento" type="text" value="{{ old('loc_complemento') }}"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="cidade" class="v-toolbar-label">Cidade <span class="text-red-500">*</span></label>
                        <input id="loc_cidade" required readonly name="loc_cidade" type="text" class="v-input mt-1">
                    </div>
                    <div>
                        <label for="estado" class="v-toolbar-label">Estado <span class="text-red-500">*</span></label>
                        <input id="loc_estado" required readonly name="loc_estado" type="text" class="v-input mt-1">
                    </div>
                    <div>
                        <label for="pais" class="v-toolbar-label">País <span class="text-red-500">*</span></label>
                        <input id="loc_pais" name="loc_pais" type="text" required readonly class="v-input mt-1">
                    </div>
                </div>
            </fieldset>

            <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                Os campos <strong>cidade</strong>, <strong>estado</strong> e <strong>país</strong> serão preenchidos automaticamente após digitar um CEP válido.
            </p>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Responsável pelo imóvel</legend>
                <div>
                    <label for="loc_responsavel_nome" class="v-toolbar-label">Nome completo (morador, locatário ou proprietário)</label>
                    <input id="loc_responsavel_nome" name="loc_responsavel_nome" type="text" value="{{ old('loc_responsavel_nome') }}" maxlength="255"
                           class="v-input mt-1"
                           placeholder="Opcional">
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Informações Complementares</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="loc_codigo" class="v-toolbar-label">Código da Localidade <span class="text-red-500">*</span></label>
                        <input id="loc_codigo" name="loc_codigo" type="number" value="{{ old('loc_codigo') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="loc_categoria" class="v-toolbar-label">Categoria da Localidade</label>
                        <input id="loc_categoria" name="loc_categoria" type="text" value="{{ old('loc_categoria') }}"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label for="loc_quarteirao" class="v-toolbar-label">Quarteirão</label>
                        <input id="loc_quarteirao" name="loc_quarteirao" type="number" value="{{ old('loc_quarteirao') }}"
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="loc_sequencia" class="v-toolbar-label">Sequência</label>
                        <input id="loc_sequencia" name="loc_sequencia" type="number" value="{{ old('loc_sequencia') }}"
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="loc_lado" class="v-toolbar-label">Lado</label>
                        <input id="loc_lado" name="loc_lado" type="number" value="{{ old('loc_lado') }}"
                            class="v-input mt-1">
                    </div>  
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                    Os campos <strong>quarteirão</strong>, <strong>sequência</strong> e <strong>lado</strong> são utilizados para identificar a localização exata do imóvel.    
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Geolocalização</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <label for="latitude" class="v-toolbar-label">Latitude <span class="text-red-500">*</span></label>
                        <input id="loc_latitude" name="loc_latitude" type="text" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <label for="longitude" class="v-toolbar-label">Longitude <span class="text-red-500">*</span></label>
                        <input id="loc_longitude" name="loc_longitude" type="text" required
                            class="v-input mt-1">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="btn-minha-localizacao" onclick="obterMinhaLocalizacao()"
                                class="v-btn-primary w-full">
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

            @include('municipio.locais._form_ocupantes')

            <div class="flex justify-end">
                <button type="submit" id="btn-cadastrar-local"
                        x-bind:disabled="carregando"
                        class="v-btn-primary px-6 disabled:opacity-50 disabled:cursor-not-allowed">
                    Cadastrar
                </button>
            </div>
        </form>
    </x-section-card>
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
    var cepValidouMunicipio = false;
    function getCepFromCadastrados(cepNorm) {
        if (!cepsCadastrados || !cepsCadastrados.length) return null;
        var key = String(cepNorm).replace(/\D/g, '').slice(0, 8);
        if (key.length !== 8) return null;
        for (var i = 0; i < cepsCadastrados.length; i++) { if (cepsCadastrados[i].cep === key) return cepsCadastrados[i]; }
        return null;
    }

    function normStr(s) { if (!s || typeof s !== 'string') return ''; return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/\s+/g,' ').trim(); }
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
    window._cepValidouMunicipio = function() { return cepValidouMunicipio; };
    window._setCepValidouMunicipio = function(v) { cepValidouMunicipio = v; };
    var formEl = document.getElementById('form_local');
    if (formEl) formEl.addEventListener('submit', function(e) {
        if (!checkCepLive()) { e.preventDefault(); return false; }
        if (!navigator.onLine && typeof window.VisitaOfflineSaveLocalDraft === 'function') {
            e.preventDefault();
            var names = ['loc_cep','loc_tipo','loc_zona','loc_endereco','loc_numero','loc_bairro','loc_cidade','loc_estado','loc_pais','loc_latitude','loc_longitude','loc_codigo','loc_quarteirao','loc_complemento','loc_categoria','loc_sequencia','loc_lado','loc_responsavel_nome'];
            var payload = {};
            names.forEach(function(n) {
                var el = document.querySelector('[name="' + n + '"]');
                var v = el ? (el.value || '').trim() : '';
                if (n === 'loc_sequencia' || n === 'loc_lado') payload[n] = v === '' ? null : (parseInt(v, 10) || null);
                else payload[n] = v || null;
            });
            if (!payload.loc_endereco || !payload.loc_cidade || !payload.loc_estado) {
                alert('Preencha pelo menos endereço, cidade e estado antes de guardar.');
                return false;
            }
            var btn = document.getElementById('btn-cadastrar-local');
            if (btn) btn.disabled = true;
            window.VisitaOfflineSaveLocalDraft('agente', payload).then(function() {
                setTimeout(function() { window.location.replace('{{ route('agente.locais.index') }}?guardada=1'); }, 100);
            }).catch(function() { if (btn) btn.disabled = false; });
            return false;
        }
    });
    function updateLocalBtnOffline() {
        var btn = document.getElementById('btn-cadastrar-local');
        if (!btn) return;
        var baseDisabled = ' disabled:opacity-50 disabled:cursor-not-allowed';
        if (!navigator.onLine) {
            btn.textContent = 'Guardar local';
            btn.className = 'v-btn-amber-solid px-6' + baseDisabled;
        } else {
            btn.textContent = 'Cadastrar';
            btn.className = 'v-btn-primary px-6' + baseDisabled;
        }
    }
    updateLocalBtnOffline();
    document.addEventListener('visita-connection-change', updateLocalBtnOffline);
    window.addEventListener('visita-connection-change', updateLocalBtnOffline);

    $('#loc_cep').mask('00000-000');

    var map = L.map('map').setView([-28.7, -52.3], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
    var marker = L.marker([-28.7, -52.3], { draggable: true, icon: L.divIcon({ className: 'custom-pin', html: pinSvg, iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40] }) }).addTo(map);

    marker.on('dragend', function(e) {
        const pos = e.target.getLatLng();
        document.getElementById('loc_latitude').value = pos.lat.toFixed(7);
        document.getElementById('loc_longitude').value = pos.lng.toFixed(7);
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
                else {
                    var msg = 'Não foi possível usar a localização do dispositivo. Informe um CEP (para preencher endereço) ou arraste o marcador no mapa / digite latitude e longitude manualmente.';
                    alert(msg);
                }
            });
        } else {
            alert('Não foi possível usar a localização do dispositivo. Arraste o marcador no mapa ou digite latitude e longitude manualmente.');
        }
    }
    navigator.geolocation.getCurrentPosition(onOk, function(err) { onErr(err, true); }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 });
}
</script>
@endsection