@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.brand') . ' · ' . __('Resultado da consulta'))
@section('og_description', isset($local) ? 'Consulta pública municipal com histórico de visitas do imóvel em ' . $local->loc_cidade . '/' . $local->loc_estado . '.' : 'Consulte o histórico de visitas do imóvel usando o código da placa.')

@section('content')
<div class="mx-auto max-w-5xl v-stack">

    {{-- Cabeçalho --}}
    <header class="v-page-header pt-8">
        <div class="flex items-center gap-3 mb-4">
            <img src="{{ asset('images/visitaai.svg') }}" alt="{{ config('app.brand') }}" class="h-12 w-auto" />
            <h1 class="v-page-title">Resultado da Consulta</h1>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" id="btn-baixar-card" aria-label="Baixar card com QR Code"
                class="v-btn-secondary inline-flex items-center gap-1.5 px-4 py-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                </svg>
                Baixar card QR Code
            </button>
            <button type="button" id="btn-compartilhar" aria-label="Compartilhar link"
                    data-url="{{ url()->current() }}"
                    data-title="{{ config('app.brand') }} · {{ __('Resultado da consulta') }}"
                class="v-btn-secondary inline-flex items-center gap-1.5 px-4 py-2">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z" />
                </svg>
                <span id="btn-copiar-texto">Compartilhar link</span>
            </button>
        </div>
    </header>

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
    <section class="v-card grid grid-cols-1 gap-6 md:grid-cols-2 items-start">
        <div class="space-y-3 text-sm text-gray-800 dark:text-gray-100">
            <h2 class="v-section-title">Imóvel Consultado</h2>
            <p>
                <strong>Zona:</strong>
                @if ($local->loc_zona === 'U')
                    <span class="inline-block bg-violet-100 text-violet-900 dark:bg-violet-900/60 dark:text-violet-200 px-2 py-0.5 rounded text-xs font-semibold">Urbana</span>
                @elseif ($local->loc_zona === 'R')
                    <span class="inline-block bg-amber-100 text-amber-800 dark:bg-amber-800 dark:text-amber-200 px-2 py-0.5 rounded text-xs font-semibold">Rural</span>
                @else
                    N/A
                @endif
            </p>
            <p>
                <strong>Tipo de Imóvel:</strong>
                @if ($local->loc_tipo === 'R')
                    <span class="inline-block bg-emerald-100 text-emerald-800 dark:bg-emerald-800 dark:text-emerald-200 px-2 py-0.5 rounded text-xs font-semibold">Residencial</span>
                @elseif ($local->loc_tipo === 'C')
                    <span class="inline-block bg-indigo-100 text-indigo-800 dark:bg-indigo-800 dark:text-indigo-200 px-2 py-0.5 rounded text-xs font-semibold">Comercial</span>
                @else
                    <span class="inline-block bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-200 px-2 py-0.5 rounded text-xs font-semibold">Terreno Baldio</span>
                @endif
            </p>
            <p><strong>Quarteirão:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_quarteirao ?? 'N/A' }}</span></p>
            <p><strong>Endereço:</strong> {{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else N/A @endif</p>
            <p><strong>Bairro:</strong> <span class="inline-block bg-slate-100 text-slate-700 dark:bg-slate-600 dark:text-slate-200 px-2 py-0.5 rounded text-xs font-medium">{{ $local->loc_bairro }}</span></p>
            <p><strong>Cidade:</strong> <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200 px-2 py-0.5 rounded text-xs font-semibold">{{ $local->loc_cidade }}/{{ $local->loc_estado }}</span></p>
            <p>
                <strong>Código de Identificação:</strong>
                <span class="inline-block bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-200 px-2 py-1 rounded text-xs font-semibold">#{{ $local->loc_codigo_unico }}</span>
            </p>
        </div>
        <div class="w-full h-64 rounded-lg overflow-hidden border border-slate-200/90 dark:border-slate-700/80" id="mapa-local"></div>
    </section>

    {{-- Histórico de Visitas (sem doenças) --}}
    <section class="v-card">
        <h2 class="v-section-title mb-4">{{ __('Histórico de visitas') }}</h2>
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Abaixo: endereço cadastrado, datas das visitas, tipo de atividade e pendência. Sem dados clínicos, sem identificar o profissional e sem exibir cadastro complementar do imóvel (ocupantes ou perfil socioeconômico) nesta consulta pública.') }}
        </p>

        @if ($visitas->isEmpty())
            <p class="text-sm text-gray-600 dark:text-gray-400 italic">Nenhuma visita registrada neste endereço até o momento.</p>
        @else
            <div class="v-table-wrap rounded-lg border border-slate-200/90 dark:border-slate-700/80">
                <table class="v-data-table">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Dia da Semana</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitas as $visita)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</td>
                                <td>
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
                                        <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2 py-1 rounded dark:bg-green-900 dark:text-green-300">
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
    <section class="v-card">
        <h2 class="v-section-title mb-2">Resumo das visitas</h2>
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
                            <span class="ml-2 text-green-600 dark:text-green-400">Concluída</span>
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
    <section class="v-card v-alert-erp space-y-2 text-sm">
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
           class="v-btn-secondary inline-flex items-center justify-center gap-2 px-6 py-2.5 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            Fazer nova consulta
        </a>
    </div>

</div>
@endsection

@push('scripts')
@php
    $numero = $local->loc_numero !== null && $local->loc_numero !== '' ? $local->loc_numero : 'N/A';
@endphp
<script type="application/json" id="consulta-publica-config">{!! json_encode([
    'codigoUnico' => $local->loc_codigo_unico,
    'map' => [
        'lat' => $local->loc_latitude,
        'lng' => $local->loc_longitude,
        'popup' => $local->loc_endereco.', '.$numero,
    ],
    'i18n' => [
        'noCoord' => __('Coordenadas indisponíveis para este local.'),
        'mapError' => __('Não foi possível carregar o mapa.'),
        'downloadFail' => __('Não foi possível gerar o download do card agora.'),
        'labelShare' => __('Compartilhar link'),
        'labelCopied' => __('Link copiado!'),
        'labelCopyError' => __('Erro ao copiar'),
        'labelShared' => __('Compartilhado!'),
        'textShare' => __('Resultado da consulta epidemiológica - Visita Ai'),
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
@vite(['resources/js/consulta-codigo.js'])
@endpush