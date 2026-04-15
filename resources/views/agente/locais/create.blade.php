<!-- resources/views/agente/locais/create.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . (($isPrimario ?? false) ? __('Novo local de referência') : __('Novo local')))
@section('og_description', __('Cadastro de imóvel para visitas de vigilância entomológica e controle vetorial.'))

@section('content')
<div class="v-page v-page--wide">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Locais'), 'url' => route('agente.locais.index')], ['label' => __('Novo')]]" />

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

    <x-page-header :eyebrow="__('Cadastro territorial')" :title="($isPrimario ?? false) ? __('Novo local de referência') : __('Novo local')" />

    <x-section-card class="v-card--tight v-card--muted border border-sky-200/70 bg-gradient-to-br from-sky-50/90 to-white dark:border-sky-900/40 dark:from-slate-900/70 dark:to-slate-900/40">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                <x-heroicon-o-wifi class="h-3.5 w-3.5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ __('Modo offline') }}
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ __('Salve o local no dispositivo quando estiver sem internet e finalize o envio na sincronização quando houver conexão.') }}
                </p>
            </div>
        </div>
    </x-section-card>

    <x-section-card class="space-y-4">
        <x-flash-alerts />

        @if ($errors->any())
            <x-alert type="error" :title="__('Corrija os erros nos campos indicados abaixo.')" :message="implode(' ', $errors->all())" />
        @endif

        <form method="POST" action="{{ route($storeRoute ?? 'agente.locais.store') }}" enctype="multipart/form-data" class="space-y-6" id="form_local"
            x-data="{ carregando: false }"
            x-on:submit="carregando = true">

            @csrf

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Características Principais</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field name="loc_tipo" :label="__('Tipo de Imóvel')" :required="true">
                        <select id="loc_tipo" name="loc_tipo" required class="v-select mt-1">
                            <option value="" disabled selected>Selecione o tipo de imóvel</option>
                            <option value="R" {{ old('loc_tipo') == 'R' ? 'selected' : '' }}>Residencial (R)</option>
                            <option value="C" {{ old('loc_tipo') == 'C' ? 'selected' : '' }}>Comercial (C)</option>
                            <option value="T" {{ old('loc_tipo') == 'T' ? 'selected' : '' }}>Terreno Baldio (T)</option>
                        </select>
                    </x-form-field>
                    <x-form-field name="loc_zona" :label="__('Zona')" :required="true">
                        <select id="loc_zona" name="loc_zona" required class="v-select mt-1">
                            <option value="" disabled selected>Selecione a zona</option>
                            <option value="U" {{ old('loc_zona') == 'U' ? 'selected' : '' }}>Urbana (U)</option>
                            <option value="R" {{ old('loc_zona') == 'R' ? 'selected' : '' }}>Rural (R)</option>
                        </select>
                    </x-form-field>
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                    Os campos acima serão utilizados em relatórios e análises de localização, certifique-se de escolher as opções corretas.
                </p>
            </fieldset> 

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Endereço Completo</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div id="wrap_loc_cep">
                        <x-input-label for="cep" :value="__('CEP')" :required="true" />
                        <input id="loc_cep" name="loc_cep" type="text" maxlength="9" placeholder="00000-000" required
                            class="cep v-input mt-1"
                            data-cep-permitido="{{ $cepPermitido ?? '' }}"
                            data-cidade-estado="{{ isset($cidadeEstado) ? json_encode($cidadeEstado) : '' }}">
                        <p id="loc_cep_erro" class="mt-1 text-sm text-red-600 dark:text-red-400 hidden" role="alert"></p>
                    </div>
                    <div>
                        <x-input-label for="loc_endereco" :value="__('Logradouro')" :required="true" />
                        <input id="loc_endereco" name="loc_endereco" type="text" value="{{ old('loc_endereco') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="numero" :value="__('Número')" />
                        <input id="loc_numero" name="loc_numero" type="number"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="loc_bairro" :value="__('Bairro/Localidade')" :required="true" />
                        <input id="loc_bairro" name="loc_bairro" type="text" value="{{ old('loc_bairro') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="loc_complemento" :value="__('Complemento')" />
                        <input id="loc_complemento" name="loc_complemento" type="text" value="{{ old('loc_complemento') }}"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="cidade" :value="__('Cidade')" :required="true" />
                        <input id="loc_cidade" required readonly name="loc_cidade" type="text" class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="estado" :value="__('Estado')" :required="true" />
                        <input id="loc_estado" required readonly name="loc_estado" type="text" class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="pais" :value="__('País')" :required="true" />
                        <input id="loc_pais" name="loc_pais" type="text" required readonly class="v-input mt-1">
                    </div>
                </div>
            </fieldset>

            <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                Cidade, estado e país serão preenchidos automaticamente com um CEP válido.
            </p>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Complementos</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="loc_codigo" :value="__('Código da Localidade')" :required="true" />
                        <input id="loc_codigo" name="loc_codigo" type="number" value="{{ old('loc_codigo') }}" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="loc_categoria" :value="__('Categoria da Localidade')" />
                        <input id="loc_categoria" name="loc_categoria" type="text" value="{{ old('loc_categoria') }}"
                            class="v-input mt-1">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <x-input-label for="loc_quarteirao" :value="__('Quarteirão')" />
                        <input id="loc_quarteirao" name="loc_quarteirao" type="number" value="{{ old('loc_quarteirao') }}"
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="loc_sequencia" :value="__('Sequência')" />
                        <input id="loc_sequencia" name="loc_sequencia" type="number" value="{{ old('loc_sequencia') }}"
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="loc_lado" :value="__('Lado')" />
                        <input id="loc_lado" name="loc_lado" type="number" value="{{ old('loc_lado') }}"
                            class="v-input mt-1">
                    </div>  
                </div>
                <p class="text-sm text-gray-600 dark:text-gray-400 italic">
                    Quarteirão, sequência e lado ajudam a localizar o imóvel com precisão.
                </p>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">Geolocalização</legend>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                    <div>
                        <x-input-label for="latitude" :value="__('Latitude')" :required="true" />
                        <input id="loc_latitude" name="loc_latitude" type="text" required
                            class="v-input mt-1">
                    </div>
                    <div>
                        <x-input-label for="longitude" :value="__('Longitude')" :required="true" />
                        <input id="loc_longitude" name="loc_longitude" type="text" required
                            class="v-input mt-1">
                    </div>
                    <div class="flex justify-end">
                        <button type="button" id="btn-minha-localizacao" data-geolocate-button="1"
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

            @include('municipio.locais._form_local_documento_posse', ['local' => null])

    </x-section-card>

    <x-section-card class="space-y-4">
            <h3 class="v-section-title">{{ __('Ficha socioeconômica do imóvel') }}</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Preencha os dados socioeconômicos em etapas. As seções abaixo podem ser abertas conforme necessário.') }}</p>

            <div class="space-y-4 border-t border-gray-200 pt-6 mt-2 dark:border-gray-600">
                <h3 class="v-section-title">{{ __('Entrevista e economia') }}</h3>
                @include('municipio.locais._form_socioeconomico_head', ['local' => null])
            </div>

            @include('municipio.locais._form_ocupantes')

            <div class="space-y-4 border-t border-gray-200 pt-6 mt-2 dark:border-gray-600">
                <h3 class="v-section-title">{{ __('Imóvel, infraestrutura e posse') }}</h3>
                @include('municipio.locais._form_socioeconomico_tail', ['local' => null])
            </div>

            <div class="flex justify-end">
                <button type="submit" id="btn-cadastrar-local"
                        x-bind:disabled="carregando"
                        class="v-btn-primary px-6 disabled:opacity-50 disabled:cursor-not-allowed">
                    Salvar
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
    var localFieldIds = [
        'loc_cep',
        'loc_endereco',
        'loc_numero',
        'loc_bairro',
        'loc_cidade',
        'loc_estado',
        'loc_pais',
        'loc_latitude',
        'loc_longitude',
        'btn-minha-localizacao',
        'map'
    ];
    window.VisitaLocalFieldIds = localFieldIds.slice();

    var cepInput = document.getElementById('loc_cep');
    var cepPermitido = (cepInput && cepInput.getAttribute('data-cep-permitido')) || '';
    cepPermitido = (cepPermitido || '').trim();
    var cepPermitidoNorm = cepPermitido ? cepPermitido.replace(/\D/g, '') : '';
    var cidadeEstadoRaw = (cepInput && cepInput.getAttribute('data-cidade-estado')) || '';
    var cidadeEstado = cidadeEstadoRaw ? (function() { try { return JSON.parse(cidadeEstadoRaw); } catch(e) { return null; } })() : null;
    var cepsCadastrados = <?php echo json_encode($cepsCadastrados ?? []); ?>;
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
            var names = ['loc_cep','loc_tipo','loc_zona','loc_endereco','loc_numero','loc_bairro','loc_cidade','loc_estado','loc_pais','loc_latitude','loc_longitude','loc_codigo','loc_quarteirao','loc_complemento','loc_categoria','loc_sequencia','loc_lado'];
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
                var indexUrl = <?php echo json_encode(route('agente.locais.index')); ?>;
                setTimeout(function() { window.location.replace(indexUrl + '?guardada=1'); }, 100);
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
            btn.textContent = 'Salvar';
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

    function parseCoord(v) {
        if (typeof v !== 'string' && typeof v !== 'number') return NaN;
        return parseFloat(String(v).replace(',', '.').trim());
    }
    function syncMapFromInputs() {
        var latEl = document.getElementById('loc_latitude');
        var lngEl = document.getElementById('loc_longitude');
        if (!latEl || !lngEl) return;
        var latN = parseCoord(latEl.value);
        var lngN = parseCoord(lngEl.value);
        if (isNaN(latN) || isNaN(lngN)) return;
        if (latN < -90 || latN > 90 || lngN < -180 || lngN > 180) return;
        if (typeof window.setMapPosition === 'function') window.setMapPosition(latN, lngN);
    }
    var latInput = document.getElementById('loc_latitude');
    var lngInput = document.getElementById('loc_longitude');
    if (latInput) {
        latInput.addEventListener('blur', syncMapFromInputs);
        latInput.addEventListener('change', syncMapFromInputs);
    }
    if (lngInput) {
        lngInput.addEventListener('blur', syncMapFromInputs);
        lngInput.addEventListener('change', syncMapFromInputs);
    }

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

    window.tryIpGeolocationFallback = function(callback) {
        var providers = [
            {
                url: 'https://ipapi.co/json/',
                parser: function(data) {
                    var lat = parseFloat(data && data.latitude);
                    var lng = parseFloat(data && data.longitude);
                    if (isNaN(lat) || isNaN(lng)) return null;
                    return { lat: lat, lng: lng, source: 'ipapi' };
                }
            },
            {
                url: 'https://ipwho.is/',
                parser: function(data) {
                    var lat = parseFloat(data && data.latitude);
                    var lng = parseFloat(data && data.longitude);
                    if (isNaN(lat) || isNaN(lng)) return null;
                    return { lat: lat, lng: lng, source: 'ipwhois' };
                }
            }
        ];
        var idx = 0;
        function next() {
            if (idx >= providers.length) { if (callback) callback(false, null); return; }
            var p = providers[idx++];
            fetch(p.url, { headers: { 'Accept': 'application/json' } })
                .then(function(r) { if (!r.ok) throw new Error('http'); return r.json(); })
                .then(function(data) {
                    var pos = p.parser(data);
                    if (!pos) return next();
                    if (typeof window.setMapPosition === 'function') window.setMapPosition(pos.lat, pos.lng);
                    if (callback) callback(true, pos);
                })
                .catch(function() { next(); });
        }
        next();
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

    var geoBtn = document.querySelector('[data-geolocate-button="1"]');
    if (geoBtn) {
        geoBtn.addEventListener('click', function() {
            obterMinhaLocalizacao();
        });
    }
});

function obterMinhaLocalizacao() {
    var btn = document.getElementById('btn-minha-localizacao');
    var defaultLabel = 'Minha Localização';
    if (btn) { btn.disabled = true; btn.textContent = 'Obtendo...'; }
    function restoreBtn() {
        if (btn) { btn.disabled = false; btn.textContent = defaultLabel; }
    }
    function fallbackByAddressOrIp() {
        if (typeof window.geocodeEndereco === 'function') {
            window.geocodeEndereco(function(okEndereco) {
                if (okEndereco) {
                    restoreBtn();
                    alert('Não foi possível usar o GPS deste dispositivo. O marcador foi posicionado pelo endereço informado.');
                    return;
                }
                if (typeof window.tryIpGeolocationFallback === 'function') {
                    window.tryIpGeolocationFallback(function(okIp) {
                        restoreBtn();
                        if (okIp) {
                            alert('GPS indisponível. Usamos uma posição aproximada por rede/IP; confirme o ponto no mapa antes de salvar.');
                        } else {
                            alert('Não foi possível capturar sua localização. Verifique permissão de localização no navegador/sistema, informe o CEP ou ajuste o ponto manualmente no mapa.');
                        }
                    });
                    return;
                }
                restoreBtn();
                alert('Não foi possível capturar sua localização. Informe o CEP ou ajuste latitude/longitude manualmente.');
            });
            return;
        }
        restoreBtn();
        alert('Não foi possível capturar sua localização. Ajuste latitude/longitude manualmente.');
    }
    var isLocalHost = ['localhost', '127.0.0.1', '::1'].indexOf(window.location.hostname) >= 0;
    var secureOk = window.isSecureContext || isLocalHost;
    if (!navigator.geolocation || !secureOk) {
        if (!secureOk) {
            alert('Seu navegador exige HTTPS para liberar GPS neste dispositivo. Vamos tentar usar endereço ou rede como fallback.');
            fallbackByAddressOrIp();
            return;
        }
        fallbackByAddressOrIp();
        return;
    }
    var attempts = [
        { enableHighAccuracy: true, timeout: 12000, maximumAge: 60000 },
        { enableHighAccuracy: false, timeout: 15000, maximumAge: 300000 },
        { enableHighAccuracy: false, timeout: 5000, maximumAge: Infinity }
    ];
    function getPosition(opts) {
        return new Promise(function(resolve, reject) {
            navigator.geolocation.getCurrentPosition(resolve, reject, opts);
        });
    }
    function runAttempts(i) {
        if (i >= attempts.length) {
            fallbackByAddressOrIp();
            return;
        }
        getPosition(attempts[i]).then(function(pos) {
            var lat = pos && pos.coords ? parseFloat(pos.coords.latitude) : NaN;
            var lng = pos && pos.coords ? parseFloat(pos.coords.longitude) : NaN;
            if (isNaN(lat) || isNaN(lng)) {
                runAttempts(i + 1);
                return;
            }
            if (typeof window.setMapPosition === 'function') window.setMapPosition(lat, lng);
            restoreBtn();
        }).catch(function(err) {
            if (err && err.code === 1) {
                fallbackByAddressOrIp();
                return;
            }
            runAttempts(i + 1);
        });
    }

    if (navigator.permissions && typeof navigator.permissions.query === 'function') {
        navigator.permissions.query({ name: 'geolocation' }).then(function(result) {
            if (result && result.state === 'denied') {
                fallbackByAddressOrIp();
                return;
            }
            runAttempts(0);
        }).catch(function() {
            runAttempts(0);
        });
    } else {
        runAttempts(0);
    }
}
</script>
@endsection