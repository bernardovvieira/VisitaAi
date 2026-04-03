@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta do imóvel'))
@section('og_description', isset($local) ? __('Histórico público de visitas de vigilância em :cidade/:uf — datas e status, sem dados sensíveis.', ['cidade' => $local->loc_cidade, 'uf' => $local->loc_estado]) : __('Resultado da consulta pelo código do imóvel.'))

@section('content')
<div class="max-w-4xl mx-auto space-y-10">

    {{-- Cabeçalho --}}
    <div class="pt-8">
        <div class="mb-4 flex flex-wrap items-center gap-3">
            <img src="{{ asset('images/visitaai_rembg.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto" />
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-blue-600 dark:text-blue-400">{{ __('Consulta pública') }}</p>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 sm:text-3xl">{{ __('Visitas registradas neste endereço') }}</h1>
            </div>
        </div>
        <p class="max-w-2xl text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            {{ __('Os dados abaixo são apenas informativos: localização cadastrada, datas das visitas e se havia pendência na época. Não substituem comunicação com a Secretaria Municipal de Saúde.') }}
        </p>
        <button type="button" id="btn-baixar-card" aria-label="{{ __('Baixar cartão com QR Code para colar no imóvel') }}"
                class="mt-4 inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            <x-heroicon-o-arrow-down-tray class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Baixar cartão com QR Code') }}
        </button>
    </div>

    {{-- Card QR Code (oculto, usado para download) --}}
    <div id="adesivo" class="fixed left-[-9999px] top-0 w-[300px] bg-white p-6 text-center">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-3">{{ config('app.name') }} · {{ __('Consulta pública') }}</p>
        <p class="text-sm text-gray-800 leading-snug mb-4">
            {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}<br>
            <span class="text-gray-600">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
        </p>
        <div class="flex justify-center">
            <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="QR Code" class="w-32 h-32 block">
        </div>
        <p class="text-[10px] text-gray-500 break-all mt-4 font-mono leading-tight px-1">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
        <p class="text-[9px] text-gray-400 mt-5">{{ __('Bitwise Technologies') }}</p>
    </div>

    {{-- Imóvel consultado --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start">
        <section class="v-card dark:bg-gray-800">
            <div class="space-y-3 text-sm text-gray-800 dark:text-gray-100">
                <h2 class="mb-2 text-xl font-semibold text-gray-800 dark:text-gray-200">{{ __('Imóvel consultado') }}</h2>
                <p>
                    <strong>{{ __('Zona') }}:</strong>
                    @if ($local->loc_zona === 'U')
                        <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ __('Urbana') }}</span>
                    @elseif ($local->loc_zona === 'R')
                        <span class="inline-block bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-200 px-2 py-0.5 rounded text-xs font-semibold">{{ __('Rural') }}</span>
                    @else
                        {{ __('N/D') }}
                    @endif
                </p>
                <p>
                    <strong>{{ __('Tipo de imóvel') }}:</strong>
                    @if ($local->loc_tipo === 'R')
                        <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ __('Residencial') }}</span>
                    @elseif ($local->loc_tipo === 'C')
                        <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ __('Comercial') }}</span>
                    @else
                        <span class="inline-block bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200 px-2 py-0.5 rounded text-xs font-semibold">{{ __('Terreno baldio') }}</span>
                    @endif
                </p>
                <p><strong>{{ __('Quarteirão') }}:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_quarteirao ?? __('N/D') }}</span></p>
                <p><strong>{{ __('Endereço') }}:</strong> {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else {{ __('N/D') }} @endif</p>
                <p><strong>{{ __('Bairro') }}:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_bairro }}</span></p>
                <p><strong>{{ __('Cidade') }}:</strong> <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ $local->loc_cidade }}/{{ $local->loc_estado }}</span></p>
                <p>
                    <strong>{{ __('Código do imóvel') }}:</strong>
                    <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ $local->loc_codigo_unico }}</span>
                </p>
            </div>
        </section>
        <section class="v-card dark:bg-gray-800" aria-label="{{ __('Mapa do imóvel') }}">
            <h2 class="mb-3 text-lg font-semibold text-gray-800 dark:text-gray-200">{{ __('Localização no mapa') }}</h2>
            <div class="h-64 w-full overflow-hidden rounded-lg border" id="mapa-local"></div>
        </section>
    </div>

    {{-- Histórico de Visitas (sem doenças) --}}
    <section class="v-card dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ __('Histórico de visitas') }}</h2>
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">{{ __('Lista ordenada pela data da visita. “Pendente” indica que havia ação em aberto naquela data; “Concluída” indica encerramento sem pendência naquele registro.') }}</p>

        @if ($visitas->isEmpty())
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Ainda não há visitas registradas para este código no sistema. Quando o ACE ou ACS realizar o primeiro registro, as datas aparecerão aqui.') }}</p>
        @else
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">{{ __('Data') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Dia da semana') }}</th>
                            <th class="px-4 py-3 text-left">{{ __('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-100">
                        @foreach ($visitas as $visita)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-600 transition">
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</td>
                                <td class="px-4 py-3">
                                    @if ($visita->vis_pendencias)
                                        @php
                                            $revisitaPosterior = $visita->local->visitas()
                                                ->where('vis_data', '>', $visita->vis_data)
                                                ->orderBy('vis_data')
                                                ->first();
                                        @endphp

                                        <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded dark:bg-red-900 dark:text-red-300">
                                            {{ __('Pendente') }}
                                        </span>

                                        @if($revisitaPosterior)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                                {{ __('Revisitado em :d', ['d' => \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y')]) }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-block rounded bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-900 dark:bg-emerald-900/45 dark:text-emerald-200">
                                            {{ __('Concluída') }}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>

    {{-- Resumos para o cidadão (texto neutro, sem dados sensíveis) --}}
    @if (!empty($resumos) && count($resumos) > 0)
    <section class="v-card dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">{{ __('Resumo em linguagem simples') }}</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            {{ __('Texto gerado a partir do registro da visita, sem dados pessoais sensíveis. Para dúvidas ou reclamações, procure a Secretaria Municipal de Saúde.') }}
        </p>
        <div class="space-y-4">
            @foreach ($visitas as $visita)
                @if (isset($resumos[$visita->vis_id]))
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        {{ __('Visita de :d', ['d' => \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y')]) }}
                        @if ($visita->vis_pendencias)
                            <span class="ml-2 text-amber-600 dark:text-amber-400">{{ __('Pendente') }}</span>
                        @else
                            <span class="ml-2 text-emerald-600 dark:text-emerald-400">{{ __('Concluída') }}</span>
                        @endif
                    </p>
                    <p class="text-sm text-gray-800 dark:text-gray-100 leading-relaxed">{{ $resumos[$visita->vis_id] }}</p>
                </div>
                @endif
            @endforeach
        </div>
    </section>
    @endif

    {{-- Informativo institucional --}}
    <section class="v-card border-amber-300/90 bg-amber-50 text-sm text-amber-950 shadow-sm space-y-2 dark:border-amber-700 dark:bg-amber-950/35 dark:text-amber-100">
        <h2 class="text-base font-semibold">{{ __('Limites desta consulta') }}</h2>
        <p>
            {{ __('A consulta pública mostra somente o que está vinculado ao código do imóvel: endereço cadastrado, datas de visita e status (pendente ou concluída). Não exibe diagnósticos, nomes de moradores ou outros dados protegidos.') }}
        </p>
        <p>
            {{ __('Para orientação sanitária, segunda via de documentos ou reclamações, fale com a Secretaria Municipal de Saúde do seu município.') }}
        </p>
    </section>

    {{-- Rodapé --}}
    <div class="flex flex-col items-center gap-4 pt-6">
        <a href="{{ route('consulta.index') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg shadow transition">
            <x-heroicon-o-magnifying-glass class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Consultar outro código') }}
        </a>
        <button type="button" id="btn-compartilhar" aria-label="{{ __('Copiar ou compartilhar o link desta página') }}"
                data-url="{{ url()->current() }}"
                data-title="{{ config('app.name') }} · {{ __('Consulta do imóvel') }}"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
            <x-heroicon-o-share class="h-4 w-4 shrink-0" aria-hidden="true" />
            <span id="btn-copiar-texto">{{ __('Copiar ou compartilhar link') }}</span>
        </button>
    </div>

</div>

{{-- Mapa + html2canvas para download do card --}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
.leaflet-marker-icon.custom-pin { background: none !important; border: none !important; }
</style>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

@php
    $numero = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : 'N/A';
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var el = document.getElementById('mapa-local');
        var tMapaOff = @json(__('Mapa indisponível. Recarregue a página ou verifique sua conexão.'));
        var tSemCoord = @json(__('Não há localização no mapa para este imóvel cadastrado.'));
        var tErroMapa = @json(__('Não foi possível carregar o mapa.'));
        var msg = function(html) { el.innerHTML = '<div class="flex items-center justify-center h-full text-center text-sm text-gray-500 dark:text-gray-400 px-4">' + html + '</div>'; };
        try {
            if (typeof L === 'undefined') {
                msg(tMapaOff);
                return;
            }
            var lat = parseFloat("{{ $local->loc_latitude ?? '' }}");
            var lng = parseFloat("{{ $local->loc_longitude ?? '' }}");
            if (isNaN(lat) || isNaN(lng) || lat === 0 && lng === 0) {
                msg(tSemCoord);
                return;
            }
            var mapa = L.map('mapa-local').setView([lat, lng], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(mapa);
            var pinSvg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="28" height="40"><path fill="#2563eb" stroke="#fff" stroke-width="1.5" d="M12 0C7.31 0 3.5 3.81 3.5 8.5c0 5.25 8.5 15.5 8.5 15.5s8.5-10.25 8.5-15.5C20.5 3.81 16.69 0 12 0z"/><circle fill="#fff" cx="12" cy="8.5" r="2.8"/></svg>';
            var pinIcon = L.divIcon({
                className: 'custom-pin',
                html: pinSvg,
                iconSize: [28, 40],
                iconAnchor: [14, 40],
                popupAnchor: [0, -40]
            });
            L.marker([lat, lng], { icon: pinIcon }).addTo(mapa)
                .bindPopup("{{ addslashes($local->loc_endereco) }}, {{ addslashes($numero) }}")
                .openPopup();
            setTimeout(function() { mapa.invalidateSize(); }, 100);
        } catch (e) {
            msg(tErroMapa);
        }
    });

    // Baixar card QR Code
    document.getElementById('btn-baixar-card')?.addEventListener('click', function () {
        var adesivo = document.getElementById('adesivo');
        if (!adesivo || typeof html2canvas === 'undefined') return;
        html2canvas(adesivo).then(function (canvas) {
            var link = document.createElement('a');
            link.download = 'adesivo_qrcode_visita_ai_{{ $local->loc_codigo_unico }}.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        });
    });

    // Compartilhar link: tenta share nativo, senão copia
    document.getElementById('btn-compartilhar')?.addEventListener('click', function () {
        var url = this.getAttribute('data-url') || window.location.href;
        var title = this.getAttribute('data-title') || document.title;
        var span = document.getElementById('btn-copiar-texto');
        var labelCompartilhar = @json(__('Copiar ou compartilhar link'));
        var labelCopiado = @json(__('Link copiado!'));
        var labelErroCopiar = @json(__('Não foi possível copiar'));
        var labelCompartilhado = @json(__('Compartilhado!'));
        var textoShare = @json(__('Consulta pública de visitas no imóvel | :app', ['app' => config('app.name')]));
        var original = span ? span.textContent : labelCompartilhar;

        function copiarFeedback() {
            if (span) {
                span.textContent = labelCopiado;
                span.classList.add('text-blue-600', 'dark:text-blue-400');
                setTimeout(function () {
                    span.textContent = original;
                    span.classList.remove('text-blue-600', 'dark:text-blue-400');
                }, 2000);
            }
        }

        function copiar() {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(copiarFeedback).catch(function () {
                    if (span) span.textContent = labelErroCopiar;
                });
            } else {
                var ta = document.createElement('textarea');
                ta.value = url;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try {
                    document.execCommand('copy');
                    copiarFeedback();
                } catch (e) {
                    if (span) span.textContent = labelErroCopiar;
                }
                document.body.removeChild(ta);
            }
        }

        if (navigator.share && typeof navigator.share === 'function') {
            navigator.share({
                title: title,
                text: textoShare,
                url: url
            }).then(function () {
                if (span) {
                    span.textContent = labelCompartilhado;
                    span.classList.add('text-blue-600', 'dark:text-blue-400');
                    setTimeout(function () {
                        span.textContent = original;
                        span.classList.remove('text-blue-600', 'dark:text-blue-400');
                    }, 2000);
                }
            }).catch(function (err) {
                if (err.name !== 'AbortError') copiar();
            });
        } else {
            copiar();
        }
    });
</script>

@endsection