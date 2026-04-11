@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta do imóvel'))
@section('og_description', isset($local) ? __('Consulta pública em :cidade/:uf: visitas de campo, vigilância em saúde conforme a operação municipal, datas e status, sem dados clínicos e sem cadastro complementar do imóvel na área pública.', ['cidade' => $local->loc_cidade, 'uf' => $local->loc_estado]) : __('Consulta pública pelo código do imóvel. Transparência sobre visitas de campo, sem cadastro complementar na página pública.'))

@section('content')
@php
    $numeroExibicao = $local->loc_numero !== null && $local->loc_numero !== '' ? (string) $local->loc_numero : __('S/N');
    $linhaPopup = $local->loc_endereco.', '.$numeroExibicao;
@endphp
<div class="welcome-public welcome-public--extend w-full min-w-0">
    <div class="public-page__stack">
        <header class="welcome-public__hero">
            <div class="welcome-public__hero-row">
                <div class="public-page__hero-aside">
                    <img
                        src="{{ asset('images/visitaai_rembg.png') }}"
                        alt="{{ __('Marca do aplicativo') }}, {{ config('app.brand') }}"
                        width="80"
                        height="80"
                        class="welcome-public__logo"
                        decoding="async" />
                </div>
                <div class="welcome-public__hero-content">
                    <p class="welcome-public__kicker">
                        {{ __('Indicadores municipais · transparência ao cidadão') }}
                    </p>
                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                        <h1 class="welcome-public__title min-w-0 shrink">
                            {{ __('Visitas registradas neste endereço') }}
                            <span class="font-medium text-slate-800 dark:text-slate-100"> {{ config('app.brand') }}</span>
                        </h1>
                        <x-public-municipality-pill :local="$localPrimario ?? null" class="shrink-0" />
                    </div>
                    <p class="welcome-public__lead">
                        {{ __('Abaixo: endereço cadastrado, datas das visitas, tipo de atividade e pendência. Sem dados clínicos, sem identificar o profissional e sem exibir cadastro complementar do imóvel (ocupantes ou perfil socioeconômico) nesta consulta pública.') }}
                    </p>
                </div>
            </div>
            <div class="public-hero-actions">
                <a href="{{ url('/') }}" class="welcome-public__link">
                    <x-heroicon-o-arrow-left class="h-3.5 w-3.5 shrink-0 opacity-80" aria-hidden="true" />
                    {{ __('Voltar à página inicial') }}
                </a>
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

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-stretch">
            <div class="consulta-public-detail flex h-full flex-col">
                <h2 class="public-section-title">{{ __('Imóvel consultado') }}</h2>
                <dl class="public-kv mt-4">
                    <dt>{{ __('Zona') }}</dt>
                    <dd>
                        @if ($local->loc_zona === 'U')
                            <span class="public-badge">{{ __('Urbana') }}</span>
                        @elseif ($local->loc_zona === 'R')
                            <span class="public-badge public-badge--amber">{{ __('Rural') }}</span>
                        @else
                            {{ __('N/D') }}
                        @endif
                    </dd>
                    <dt>{{ __('Tipo de imóvel') }}</dt>
                    <dd>
                        @if ($local->loc_tipo === 'R')
                            <span class="public-badge">{{ __('Residencial') }}</span>
                        @elseif ($local->loc_tipo === 'C')
                            <span class="public-badge">{{ __('Comercial') }}</span>
                        @else
                            <span class="public-badge public-badge--muted">{{ __('Terreno baldio') }}</span>
                        @endif
                    </dd>
                    <dt>{{ __('Quarteirão') }}</dt>
                    <dd><span class="public-badge public-badge--muted">{{ $local->loc_quarteirao ?? __('N/D') }}</span></dd>
                    <dt>{{ __('Endereço') }}</dt>
                    <dd>{{ $local->loc_endereco }}, @if($local->loc_numero) {{ $local->loc_numero }} @else {{ __('S/N') }} @endif</dd>
                    <dt>{{ __('Bairro') }}</dt>
                    <dd><span class="public-badge public-badge--muted">{{ $local->loc_bairro }}</span></dd>
                    <dt>{{ __('Cidade') }}</dt>
                    <dd><span class="public-badge">{{ $local->loc_cidade }}/{{ $local->loc_estado }}</span></dd>
                    <dt>{{ __('Código do imóvel') }}</dt>
                    <dd><span class="public-badge font-mono tabular-nums tracking-tight">{{ $local->loc_codigo_unico }}</span></dd>
                </dl>
            </div>
            <div class="consulta-public-detail flex h-full min-h-0 flex-col" aria-label="{{ __('Mapa do imóvel') }}">
                <h2 class="public-section-title shrink-0">{{ __('Localização no mapa') }}</h2>
                <div class="mt-4 w-full flex-1 min-h-[13rem] overflow-hidden md:min-h-0 relative" id="mapa-local" role="region" aria-label="{{ __('Mapa interativo') }}"></div>
            </div>
        </div>

        <div class="consulta-public-panel v-card--flush overflow-hidden p-0">
            <div class="consulta-public-panel__head">
                <h2 class="public-section-title">{{ __('Histórico de visitas') }}</h2>
                <p class="public-section-lead">{{ __('Ordenado da visita mais recente para a mais antiga.') }}</p>
            </div>

            @if ($visitas->isEmpty())
                <div class="public-empty text-left sm:text-center">
                    <p class="public-help-text max-w-xl sm:mx-auto">{{ __('Ainda não há visitas registradas para este código no sistema. Quando o ACE ou ACS realizar o primeiro registro, as datas aparecerão aqui.') }}</p>
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
                                    <th scope="row" class="max-w-[10rem] text-left font-normal leading-tight sm:max-w-none">
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

        @if (!empty($resumos) && count($resumos) > 0)
            <div class="consulta-public-detail">
                <h2 class="public-section-title">{{ __('Resumo em linguagem simples') }}</h2>
                <p class="public-section-lead max-w-none">
                    {{ __('Texto gerado a partir do registro da visita, sem dados pessoais sensíveis. Para dúvidas ou reclamações, procure a Secretaria Municipal de Saúde.') }}
                </p>
                <div class="mt-4 space-y-4">
                    @foreach ($visitas as $visita)
                        @if (isset($resumos[$visita->vis_id]))
                            <div class="consulta-public-resumo">
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

        <div class="public-actions-footer">
            <button type="button" id="btn-compartilhar" aria-label="{{ __('Copiar ou compartilhar o link desta página') }}"
                    data-url="{{ url()->current() }}"
                    data-title="{{ config('app.name') }} · {{ __('Consulta do imóvel') }}"
                    class="v-btn-secondary inline-flex min-h-[2.75rem] items-center justify-center gap-2 px-5 text-sm font-medium sm:self-center">
                <x-heroicon-o-share class="h-4 w-4 shrink-0" aria-hidden="true" />
                <span id="btn-copiar-texto">{{ __('Copiar ou compartilhar link') }}</span>
            </button>
        </div>

        @include('partials.public-copyright-footer', ['footerClass' => 'mt-2'])
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
