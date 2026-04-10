{{--
    Detalhes do imóvel: cadastro, mapa e adesivo (consulta pública).
    Variáveis: $local, $qrCodeBase64, $qrCodeMime; opcional: $fichaPdfUrl (string|null).
--}}
@php
    $na = __('N/D');
    $sn = __('S/N');
    $tipoLabel = match ($local->loc_tipo) {
        'R' => __('Residencial'),
        'C' => __('Comercial'),
        'T' => __('Terreno baldio'),
        default => $na,
    };
    $zonaLabel = match ($local->loc_zona) {
        'U' => __('Urbana'),
        'R' => __('Rural'),
        default => $na,
    };
    $numeroExibicao = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : $sn;
@endphp

@if(! empty($fichaPdfUrl))
    <p class="mb-4">
        <a href="{{ $fichaPdfUrl }}"
           class="v-btn-export v-btn-export--pdf inline-flex no-underline">
            <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Baixar ficha socioeconômica (PDF)') }}
        </a>
    </p>
@endif

<x-section-card class="space-y-5">
    <h2 class="v-section-title">{{ __('Dados cadastrais') }}</h2>
    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-4">
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Código único do imóvel') }}</dt>
            <dd class="mt-1">
                <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">
                    {{ $local->loc_codigo_unico }}
                </span>
                @if($local->isPrimary())
                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Primário') }}</span>
                @endif
            </dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Código da localidade') }}</dt>
            <dd class="mt-1">{{ $local->loc_codigo ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Categoria da localidade') }}</dt>
            <dd class="mt-1">{{ $local->loc_categoria ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Tipo') }}</dt>
            <dd class="mt-1">{{ $tipoLabel }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Zona') }}</dt>
            <dd class="mt-1">{{ $zonaLabel }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Quarteirão') }}</dt>
            <dd class="mt-1">{{ $local->loc_quarteirao ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Sequência') }}</dt>
            <dd class="mt-1">{{ $local->loc_sequencia ?? $na }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Lado') }}</dt>
            <dd class="mt-1">{{ $local->loc_lado ?? $na }}</dd>
        </div>
        <div class="sm:col-span-4" style="padding-top: 0.5rem;">
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Endereço completo') }}</dt>
            <dd class="mt-1 text-gray-900 dark:text-gray-100">
                {{ $local->loc_endereco }}, {{ $numeroExibicao }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}, {{ $local->loc_pais }}
                <span class="text-slate-500 dark:text-slate-400"> · </span>{{ __('CEP') }}: {{ $local->loc_cep }}<br>
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ __('Complemento') }}: {{ $local->loc_complemento ?? $na }}</span>
            </dd>
        </div>
        <div class="sm:col-span-4">
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Responsável pelo imóvel') }}</dt>
            <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_responsavel_nome ?? __('Não informado') }}</dd>
        </div>
    </dl>

    <dl class="grid grid-cols-1 gap-x-6 gap-y-4 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-2">
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Latitude') }}</dt>
            <dd class="mt-1 tabular-nums">{{ $local->loc_latitude }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Longitude') }}</dt>
            <dd class="mt-1 tabular-nums">{{ $local->loc_longitude }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Criado em') }}</dt>
            <dd class="mt-1">{{ $local->created_at->format('d/m/Y H:i') }}</dd>
        </div>
        <div>
            <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Atualizado em') }}</dt>
            <dd class="mt-1">{{ $local->updated_at->format('d/m/Y H:i') }}</dd>
        </div>
    </dl>

    <div>
        <h3 class="mb-2 text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Localização no mapa') }}</h3>
        <div id="map" class="h-72 rounded-lg border border-gray-300 shadow-sm dark:border-gray-600 dark:shadow-none"></div>
        <p class="mt-2 text-sm italic text-gray-600 dark:text-gray-400">
            {{ __('A posição exibida é baseada nas coordenadas fornecidas.') }}
        </p>
    </div>
</x-section-card>

<x-section-card class="mt-6 space-y-4">
    <h2 class="border-b border-gray-200 pb-2 text-lg font-semibold text-gray-800 dark:border-gray-700 dark:text-gray-200">
        {{ __('Adesivo para consulta pública') }}
    </h2>
    <div class="flex justify-center bg-gray-100 p-8 dark:bg-gray-800/80">
        <div id="adesivo" class="w-[300px] bg-white p-6 text-center text-gray-800 shadow-sm dark:bg-gray-900 dark:text-gray-100">
            <p class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
                {{ __('Visita Aí | Consulta pública') }}
            </p>
            <p class="mb-4 text-sm leading-snug">
                {{ $local->loc_endereco }}, {{ $numeroExibicao }}<br>
                <span class="text-gray-600 dark:text-gray-400">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
            </p>
            <div class="flex justify-center">
                <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="{{ __('QR Code para consulta pública') }}" class="block h-32 w-32">
            </div>
            <p class="mt-4 break-all px-1 text-center font-mono text-[10px] leading-tight text-gray-500 dark:text-gray-400">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
            <p class="mt-5 text-[9px] text-gray-400 dark:text-gray-500">{{ __('Bitwise Technologies') }}</p>
        </div>
    </div>

    <div class="pt-4 text-center">
        <button type="button" onclick="baixarAdesivo()"
                class="v-btn-slate mt-4 inline-flex gap-2">
            <x-heroicon-o-arrow-down-tray class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Salvar adesivo') }}
        </button>
        <p class="mt-3 pt-2 text-sm text-gray-600 dark:text-gray-400">
            {{ __('O adesivo pode ser impresso e colado no local para facilitar o acesso à consulta pública.') }}
        </p>
    </div>
</x-section-card>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
@php
    $numeroPopup = addslashes((string) $numeroExibicao);
    $endPopup = addslashes((string) $local->loc_endereco);
@endphp
<script>
document.addEventListener('DOMContentLoaded', () => {
    const el = document.getElementById('map');
    const showMapMessage = (text) => {
        while (el.firstChild) el.removeChild(el.firstChild);
        const wrap = document.createElement('div');
        wrap.className = 'flex items-center justify-center h-full text-center text-sm text-gray-500 dark:text-gray-400 px-4';
        wrap.appendChild(document.createTextNode(text));
        el.appendChild(wrap);
    };
    const MAP_UNAVAILABLE = @json(__('Mapa indisponível. Recarregue a página ou verifique sua conexão.'));
    const NO_COORDS = @json(__('Coordenadas indisponíveis para este local.'));
    const MAP_FAIL = @json(__('Não foi possível carregar o mapa.'));
    try {
        if (typeof L === 'undefined') { showMapMessage(MAP_UNAVAILABLE); return; }
        const lat = parseFloat("{{ $local->loc_latitude ?? '' }}");
        const lng = parseFloat("{{ $local->loc_longitude ?? '' }}");
        if (isNaN(lat) || isNaN(lng) || (lat === 0 && lng === 0)) { showMapMessage(NO_COORDS); return; }
        const map = L.map('map').setView([lat, lng], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { attribution: '&copy; OpenStreetMap contributors' }).addTo(map);
        const pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
        const pinIcon = L.divIcon({ className: 'custom-pin', html: pinSvg, iconSize: [28, 40], iconAnchor: [14, 40], popupAnchor: [0, -40] });
        L.marker([lat, lng], { icon: pinIcon }).addTo(map)
            .bindPopup("{{ $endPopup }}, {{ $numeroPopup }}")
            .openPopup();
        setTimeout(() => map.invalidateSize(), 100);
    } catch (e) { showMapMessage(MAP_FAIL); }
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
