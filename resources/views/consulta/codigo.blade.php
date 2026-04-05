@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta do imóvel'))
@section('og_description', isset($local) ? __('Histórico público de visitas de vigilância em :cidade/:uf; datas e status, sem dados sensíveis.', ['cidade' => $local->loc_cidade, 'uf' => $local->loc_estado]) : __('Resultado da consulta pelo código do imóvel.'))

@section('content')
@php
    $numeroExibicao = $local->loc_numero !== null && $local->loc_numero !== '' ? (string) $local->loc_numero : __('S/N');
    $linhaPopup = $local->loc_endereco.', '.$numeroExibicao;
@endphp
<div class="welcome-public welcome-public--wide w-full min-w-0">
    <div class="w-full space-y-9 lg:space-y-11">
        <header class="welcome-public__hero">
            <div class="welcome-public__hero-row">
                <div class="flex shrink-0 flex-col items-start gap-4">
                    <img
                        src="{{ asset('images/visitaai_rembg.png') }}"
                        alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                        width="96"
                        height="96"
                        class="welcome-public__logo"
                        decoding="async" />
                    <a href="{{ url('/') }}" class="welcome-public__link">
                        <x-heroicon-o-arrow-left class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                        {{ __('Voltar à página inicial') }}
                    </a>
                </div>
                <div class="min-w-0 flex-1 space-y-3 pt-0.5">
                    <p class="welcome-public__kicker">
                        {{ __('Vigilância entomológica e controle de vetores') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                        <h1 class="welcome-public__title min-w-0 shrink">
                            {{ __('Visitas registradas neste endereço') }}
                        </h1>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <span class="text-sm font-medium text-slate-800 dark:text-slate-100">{{ config('app.brand') }}</span>
                            <x-public-municipality-pill :local="$localPrimario ?? null" />
                        </div>
                    </div>
                    <p class="welcome-public__lead">
                        {{ __('Abaixo: endereço cadastrado, datas das visitas, tipo de atividade e pendência. Sem dados clínicos e sem identificar o profissional.') }}
                    </p>
                    <div class="flex flex-wrap items-center gap-x-4 gap-y-2 border-t border-slate-200/50 pt-3 dark:border-slate-800/70">
                        <button type="button" id="btn-baixar-card" aria-label="{{ __('Baixar cartão com QR Code para colar no imóvel') }}"
                                class="welcome-public__link-btn">
                            <x-heroicon-o-arrow-down-tray class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                            {{ __('Baixar cartão com QR Code') }}
                        </button>
                        <a href="{{ route('consulta.index') }}" class="welcome-public__link">
                            <x-heroicon-o-magnifying-glass class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                            {{ __('Consultar outro código') }}
                        </a>
                    </div>
                </div>
            </div>
        </header>

    {{-- Card QR Code (oculto, usado para download) --}}
    <div id="adesivo" class="fixed left-[-9999px] top-0 w-[300px] bg-white p-6 text-center">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-3">{{ config('app.name') }} · {{ __('Consulta pública') }}</p>
        <p class="text-sm text-gray-800 leading-snug mb-4">
            {{ $local->loc_endereco }}, {{ $numeroExibicao }}<br>
            <span class="text-gray-600">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
        </p>
        <div class="flex justify-center">
            <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="{{ __('QR Code: consulta pública deste imóvel') }}" class="w-32 h-32 block" width="128" height="128">
        </div>
        <p class="text-[10px] text-gray-500 break-all mt-4 font-mono leading-tight px-1">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
        <p class="text-[9px] text-gray-400 mt-5">{{ __('Bitwise Technologies') }}</p>
    </div>

    {{-- Imóvel consultado --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-stretch">
        <div class="welcome-public__surface flex h-full flex-col p-6 sm:p-7">
            <div class="space-y-3 text-sm text-slate-800 dark:text-slate-100">
                <h2 class="v-section-title mb-1">{{ __('Imóvel consultado') }}</h2>
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
        </div>
        <div class="welcome-public__surface flex h-full min-h-0 flex-col p-6 sm:p-7" aria-label="{{ __('Mapa do imóvel') }}">
            <h2 class="v-section-title mb-3 shrink-0">{{ __('Localização no mapa') }}</h2>
            <div class="w-full flex-1 overflow-hidden relative min-h-[13rem] md:min-h-0" id="mapa-local" role="region" aria-label="{{ __('Mapa interativo') }}"></div>
        </div>
    </div>

    {{-- Histórico de visitas (colunas alinhadas à listagem interna, sem profissional) --}}
    <div class="v-card--flush welcome-public__surface overflow-hidden p-0">
        <div class="p-5 sm:p-6">
            <h2 class="v-section-title">{{ __('Histórico de visitas') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('Ordenado da visita mais recente para a mais antiga.') }}</p>
        </div>

        @if ($visitas->isEmpty())
            <div class="border-t border-slate-200/80 px-4 py-8 dark:border-slate-700/80 sm:px-5">
                <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Ainda não há visitas registradas para este código no sistema. Quando o ACE ou ACS realizar o primeiro registro, as datas aparecerão aqui.') }}</p>
            </div>
        @else
            <div class="v-table-meta border-t border-slate-200/80 dark:border-slate-700/80">
                <span>{{ __('Foram encontradas :total visita(s) para este imóvel.', ['total' => $visitas->count()]) }}</span>
            </div>
            <div class="v-table-wrap">
                <table class="v-data-table">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Data') }}</th>
                            <th scope="col">{{ __('Atividade') }}</th>
                            <th scope="col">{{ __('Pendência') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($visitas as $visita)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40">
                                <th scope="row" class="max-w-[10rem] leading-tight text-left font-normal sm:max-w-none">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</div>
                                </th>
                                <td class="text-slate-600 dark:text-slate-300" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                                    {{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: __('Não informado') }}
                                </td>
                                <td>
                                    @if ($visita->vis_pendencias)
                                        <span class="inline-flex rounded-md bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-900 dark:bg-red-950/60 dark:text-red-200">{{ __('Pendente') }}</span>
                                        @if (!empty($revisitaPosterior[$visita->vis_id]))
                                            @php $rev = $revisitaPosterior[$visita->vis_id]; @endphp
                                            <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Revisitado em :d', ['d' => \Carbon\Carbon::parse($rev->vis_data)->format('d/m/Y')]) }}</div>
                                        @endif
                                    @else
                                        <span class="inline-flex rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-950/45 dark:text-emerald-200">{{ __('Concluída') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Resumos para o cidadão (texto neutro, sem dados sensíveis) --}}
    @if (!empty($resumos) && count($resumos) > 0)
    <div class="welcome-public__surface p-6 sm:p-7">
        <h2 class="v-section-title mb-2 font-medium">{{ __('Resumo em linguagem simples') }}</h2>
        <p class="mb-4 text-sm font-normal text-slate-500 dark:text-slate-400">
            {{ __('Texto gerado a partir do registro da visita, sem dados pessoais sensíveis. Para dúvidas ou reclamações, procure a Secretaria Municipal de Saúde.') }}
        </p>
        <div class="space-y-4">
            @foreach ($visitas as $visita)
                @if (isset($resumos[$visita->vis_id]))
                <div class="rounded-lg border border-slate-200/50 bg-slate-50/50 p-4 dark:border-slate-700/45 dark:bg-slate-800/25">
                    <p class="mb-1 text-xs font-medium text-slate-500 dark:text-slate-400">
                        {{ __('Visita de :d', ['d' => \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y')]) }}
                        @if ($visita->vis_pendencias)
                            <span class="ml-2 text-amber-600 dark:text-amber-400">{{ __('Pendente') }}</span>
                        @else
                            <span class="ml-2 text-emerald-600 dark:text-emerald-400">{{ __('Concluída') }}</span>
                        @endif
                    </p>
                    <p class="text-sm leading-relaxed text-slate-800 dark:text-slate-100">{{ $resumos[$visita->vis_id] }}</p>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    {{-- Rodapé --}}
    <div class="flex flex-col items-center gap-3 border-t border-slate-200/50 pt-8 dark:border-slate-800/70">
        <button type="button" id="btn-compartilhar" aria-label="{{ __('Copiar ou compartilhar o link desta página') }}"
                data-url="{{ url()->current() }}"
                data-title="{{ config('app.name') }} · {{ __('Consulta do imóvel') }}"
                class="v-btn-secondary inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium">
            <x-heroicon-o-share class="h-4 w-4 shrink-0" aria-hidden="true" />
            <span id="btn-copiar-texto">{{ __('Copiar ou compartilhar link') }}</span>
        </button>
    </div>

    @include('partials.public-copyright-footer', ['footerClass' => 'mt-8'])

    </div>
</div>

@endsection

@push('scripts')
@php
    $consultaPublicaConfig = [
        'codigoUnico' => $local->loc_codigo_unico,
        'map' => [
            'lat' => $local->loc_latitude,
            'lng' => $local->loc_longitude,
            'popup' => $linhaPopup,
        ],
        'i18n' => [
            'noCoord' => __('Não há localização no mapa para este imóvel cadastrado.'),
            'mapError' => __('Não foi possível carregar o mapa.'),
            'downloadFail' => __('Não foi possível gerar a imagem do cartão. Tente novamente.'),
            'labelShare' => __('Copiar ou compartilhar link'),
            'labelCopied' => __('Link copiado!'),
            'labelCopyError' => __('Não foi possível copiar'),
            'labelShared' => __('Compartilhado!'),
            'textShare' => __('Consulta pública de visitas no imóvel | :app', ['app' => config('app.name')]),
        ],
    ];
@endphp
<script type="application/json" id="consulta-publica-config">@json($consultaPublicaConfig)</script>
@vite(['resources/js/consulta-codigo.js'])
@endpush
