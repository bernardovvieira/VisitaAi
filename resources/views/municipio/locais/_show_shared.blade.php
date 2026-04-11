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

<x-section-card class="space-y-5">
    <h2 class="v-section-title">{{ __('Dados cadastrais') }}</h2>
    <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">
        <div class="space-y-5 xl:col-span-2">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-3 text-sm text-gray-700 dark:text-gray-300 sm:grid-cols-3">
                <div class="sm:col-span-2">
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Código único do imóvel') }}</dt>
                    <dd class="mt-1">
                        <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ $local->loc_codigo_unico }}</span>
                        @if($local->isPrimary())
                            <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Primário') }}</span>
                        @endif
                    </dd>
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
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Código da localidade') }}</dt>
                    <dd class="mt-1">{{ $local->loc_codigo ?? $na }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Categoria') }}</dt>
                    <dd class="mt-1">{{ $local->loc_categoria ?? $na }}</dd>
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
                <div>
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Criado em') }}</dt>
                    <dd class="mt-1">{{ $local->created_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Atualizado em') }}</dt>
                    <dd class="mt-1">{{ $local->updated_at->format('d/m/Y H:i') }}</dd>
                </div>
                <div class="sm:col-span-3 border-t border-slate-200/80 pt-3 dark:border-slate-700/70">
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Endereço completo') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">
                        {{ $local->loc_endereco }}, {{ $numeroExibicao }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}, {{ $local->loc_pais }}
                        <span class="text-slate-500 dark:text-slate-400"> · </span>{{ __('CEP') }}: {{ $local->loc_cep }}
                        @if($local->loc_complemento)
                            <span class="text-slate-500 dark:text-slate-400"> · </span>{{ __('Complemento') }}: {{ $local->loc_complemento }}
                        @endif
                    </dd>
                </div>
                <div class="sm:col-span-3">
                    <dt class="font-medium text-slate-700 dark:text-slate-200">{{ __('Responsável pelo imóvel') }}</dt>
                    <dd class="mt-1 text-gray-900 dark:text-gray-100">{{ $local->loc_responsavel_nome ?? __('Não informado') }}</dd>
                </div>
            </dl>
        </div>

        <aside class="space-y-3 xl:col-span-1">
            <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Localização no mapa') }}</h3>
            <div id="map" class="h-52 rounded-lg border border-gray-300 shadow-sm dark:border-gray-600 dark:shadow-none"></div>
            <dl class="grid grid-cols-1 gap-2 rounded-lg border border-slate-200/80 bg-slate-50/70 p-3 text-xs dark:border-slate-700/70 dark:bg-slate-900/45">
                <div>
                    <dt class="font-medium text-slate-500 dark:text-slate-400">{{ __('Latitude') }}</dt>
                    <dd class="tabular-nums text-slate-800 dark:text-slate-100">{{ $local->loc_latitude }}</dd>
                </div>
                <div>
                    <dt class="font-medium text-slate-500 dark:text-slate-400">{{ __('Longitude') }}</dt>
                    <dd class="tabular-nums text-slate-800 dark:text-slate-100">{{ $local->loc_longitude }}</dd>
                </div>
            </dl>
            <p class="text-xs italic text-gray-600 dark:text-gray-400">{{ __('A posição exibida é baseada nas coordenadas fornecidas.') }}</p>
        </aside>
    </div>

    <div class="mt-6 border-t border-slate-200/90 pt-5 dark:border-slate-700/80">
        <h3 class="v-section-title mb-3">{{ __('Consulta pública · adesivo') }}</h3>
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex min-w-0 items-start gap-3">
                <img
                    src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}"
                    alt=""
                    width="56"
                    height="56"
                    class="h-14 w-14 shrink-0 rounded-md border border-slate-200 bg-white object-contain dark:border-slate-600 dark:bg-slate-900"
                    decoding="async"
                />
                <div class="min-w-0 text-xs leading-snug text-slate-600 dark:text-slate-400">
                    <p class="font-medium text-slate-800 dark:text-slate-200">{{ __('QR para o morador consultar visitas pelo código.') }}</p>
                    <p class="mt-1 break-all font-mono text-[10px] text-slate-500 dark:text-slate-500">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
                </div>
            </div>
            <div class="flex shrink-0 flex-col items-stretch gap-2 sm:items-end">
                <button type="button" onclick="baixarAdesivo()"
                        class="v-btn-slate inline-flex items-center justify-center gap-2">
                    <x-heroicon-o-arrow-down-tray class="h-4 w-4 shrink-0" aria-hidden="true" />
                    {{ __('Salvar adesivo') }}
                </button>
                <p class="max-w-xs text-[11px] text-slate-500 dark:text-slate-400 sm:text-right">
                    {{ __('Gera imagem para imprimir e colar no imóvel.') }}
                </p>
            </div>
        </div>
    </div>
</x-section-card>

{{-- Layout completo só para exportação PNG (fora da tela) --}}
<div id="adesivo" class="fixed left-[-9999px] top-0 w-[300px] bg-white p-6 text-center text-gray-800 shadow-sm dark:bg-gray-900 dark:text-gray-100" aria-hidden="true">
    <p class="mb-3 text-[11px] font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">
        {{ __('Visita Aí | Consulta pública') }}
    </p>
    <p class="mb-4 text-sm leading-snug">
        {{ $local->loc_endereco }}, {{ $numeroExibicao }}<br>
        <span class="text-gray-600 dark:text-gray-400">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
    </p>
    <div class="flex justify-center">
        <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="" class="block h-32 w-32" width="128" height="128">
    </div>
    <p class="mt-4 break-all px-1 text-center font-mono text-[10px] leading-tight text-gray-500 dark:text-gray-400">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
    <p class="mt-5 text-[9px] text-gray-400 dark:text-gray-500">{{ __('Bitwise Technologies') }}</p>
</div>

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
