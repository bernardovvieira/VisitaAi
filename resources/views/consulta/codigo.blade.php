@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' — Resultado da Consulta')
@section('og_description', isset($local) ? 'Histórico de visitas do imóvel em ' . $local->loc_cidade . '/' . $local->loc_estado . '.' : 'Consulte o resultado da sua busca pelo código do imóvel.')

@section('content')
<div class="max-w-4xl mx-auto space-y-10">

    {{-- Cabeçalho --}}
    <div style="padding-top: 2rem;">
        <div class="flex items-center gap-3 mb-4">
            <img src="{{ asset('images/visitaai_rembg.png') }}" alt="{{ config('app.name') }}" class="h-12 w-auto" />
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">Resultado da Consulta</h1>
        </div>
        <button type="button" id="btn-baixar-card" aria-label="Baixar card com QR Code"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
            </svg>
            Baixar card QR Code
        </button>
    </div>

    {{-- Card QR Code (oculto, usado para download) --}}
    <div id="adesivo" class="fixed left-[-9999px] top-0 w-[300px] bg-white p-6 text-center">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-3">Visita Aí — Consulta Pública</p>
        <p class="text-sm text-gray-800 leading-snug mb-4">
            {{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}<br>
            <span class="text-gray-600">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
        </p>
        <div class="flex justify-center">
            <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="QR Code" class="w-32 h-32 block">
        </div>
        <p class="text-[10px] text-gray-500 break-all mt-4 font-mono leading-tight px-1">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
        <p class="text-[9px] text-gray-400 mt-5">Bitwise Technologies</p>
    </div>

    {{-- Endereço e mapa --}}
    <section class="rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 p-6 shadow-sm grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
        <div class="space-y-3 text-sm text-gray-800 dark:text-gray-100">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Imóvel Consultado</h2>
            <p>
                <strong>Zona:</strong>
                @if ($local->loc_zona === 'U')
                    <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">Urbana</span>
                @elseif ($local->loc_zona === 'R')
                    <span class="inline-block bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-200 px-2 py-0.5 rounded text-xs font-semibold">Rural</span>
                @else
                    N/A
                @endif
            </p>
            <p>
                <strong>Tipo de Imóvel:</strong>
                @if ($local->loc_tipo === 'R')
                    <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">Residencial</span>
                @elseif ($local->loc_tipo === 'C')
                    <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">Comercial</span>
                @else
                    <span class="inline-block bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200 px-2 py-0.5 rounded text-xs font-semibold">Terreno Baldio</span>
                @endif
            </p>
            <p><strong>Quarteirão:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_quarteirao ?? 'N/A' }}</span></p>
            <p><strong>Endereço:</strong> {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif</p>
            <p><strong>Bairro:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_bairro }}</span></p>
            <p><strong>Cidade:</strong> <span class="inline-block rounded bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ $local->loc_cidade }}/{{ $local->loc_estado }}</span></p>
            <p>
                <strong>Código de Identificação:</strong>
                <span class="inline-block rounded bg-slate-100 px-2 py-1 font-mono text-xs font-semibold tracking-tight text-slate-800 dark:bg-slate-700 dark:text-slate-200">{{ $local->loc_codigo_unico }}</span>
            </p>
        </div>
        <div class="w-full h-64 rounded-lg overflow-hidden border" id="mapa-local"></div>
    </section>

    {{-- Histórico de Visitas (sem doenças) --}}
    <section class="rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Histórico de Visitas</h2>

        @if ($visitas->isEmpty())
            <p class="text-sm text-gray-600 dark:text-gray-400 italic">Nenhuma visita registrada neste endereço até o momento.</p>
        @else
            <div class="overflow-x-auto rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                    <thead class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold">
                        <tr>
                            <th class="px-4 py-3 text-left">Data</th>
                            <th class="px-4 py-3 text-left">Dia da Semana</th>
                            <th class="px-4 py-3 text-left">Status</th>
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
                                            Pendente
                                        </span>

                                        @if($revisitaPosterior)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                                Revisitado em {{ \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y') }}
                                            </div>
                                        @endif
                                    @else
                                        <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-medium px-2 py-1 rounded dark:bg-emerald-900 dark:text-emerald-300">
                                            Concluída
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
    <section class="rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Resumo das visitas</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            O texto abaixo é apenas informativo. Em caso de dúvidas, procure a Secretaria Municipal de Saúde.
        </p>
        <div class="space-y-4">
            @foreach ($visitas as $visita)
                @if (isset($resumos[$visita->vis_id]))
                <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        Visita de {{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}
                        @if ($visita->vis_pendencias)
                            <span class="ml-2 text-amber-600 dark:text-amber-400">Pendente</span>
                        @else
                            <span class="ml-2 text-emerald-600 dark:text-emerald-400">Concluída</span>
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
    <section class="rounded-xl border border-amber-300/90 bg-amber-50 p-6 text-sm text-amber-950 shadow-sm space-y-2 dark:border-amber-700 dark:bg-amber-950/35 dark:text-amber-100">
        <h2 class="text-base font-semibold">Precisa de mais informações?</h2>
        <p>
            Esta consulta pública tem caráter informativo e exibe apenas datas e status das visitas realizadas no endereço.
        </p>
        <p>
            Para esclarecimentos sobre o imóvel, entre em contato diretamente com a <strong>Secretaria Municipal de Saúde</strong>.
        </p>
    </section>

    {{-- Rodapé --}}
    <div class="flex flex-col items-center gap-4 pt-6">
        <a href="{{ route('consulta.index') }}"
           class="inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm font-medium text-white bg-gray-600 hover:bg-gray-700 rounded-lg shadow transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Fazer nova consulta
        </a>
        <button type="button" id="btn-compartilhar" aria-label="Compartilhar link"
                data-url="{{ url()->current() }}"
                data-title="{{ config('app.name') }} — Resultado da Consulta"
                class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-600 dark:text-emerald-400 hover:underline">
            <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
            </svg>
            <span id="btn-copiar-texto">Compartilhar link</span>
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
        var msg = function(html) { el.innerHTML = '<div class="flex items-center justify-center h-full text-center text-sm text-gray-500 dark:text-gray-400 px-4">' + html + '</div>'; };
        try {
            if (typeof L === 'undefined') {
                msg('Mapa indisponível (recarregue a página ou verifique sua conexão).');
                return;
            }
            var lat = parseFloat("{{ $local->loc_latitude ?? '' }}");
            var lng = parseFloat("{{ $local->loc_longitude ?? '' }}");
            if (isNaN(lat) || isNaN(lng) || lat === 0 && lng === 0) {
                msg('Coordenadas indisponíveis para este local.');
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
            msg('Não foi possível carregar o mapa.');
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
        var original = span ? span.textContent : 'Compartilhar link';

        function copiarFeedback() {
            if (span) {
                span.textContent = 'Link copiado!';
                span.classList.add('text-emerald-600', 'dark:text-emerald-400');
                setTimeout(function () {
                    span.textContent = original;
                    span.classList.remove('text-emerald-600', 'dark:text-emerald-400');
                }, 2000);
            }
        }

        function copiar() {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(url).then(copiarFeedback).catch(function () {
                    if (span) span.textContent = 'Erro ao copiar';
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
                    if (span) span.textContent = 'Erro ao copiar';
                }
                document.body.removeChild(ta);
            }
        }

        if (navigator.share && typeof navigator.share === 'function') {
            navigator.share({
                title: title,
                text: 'Resultado da consulta — Visita Aí',
                url: url
            }).then(function () {
                if (span) {
                    span.textContent = 'Compartilhado!';
                    span.classList.add('text-emerald-600', 'dark:text-emerald-400');
                    setTimeout(function () {
                        span.textContent = original;
                        span.classList.remove('text-emerald-600', 'dark:text-emerald-400');
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