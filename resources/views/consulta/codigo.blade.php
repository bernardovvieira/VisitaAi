@extends('layouts.app')

@section('public')
@endsection

@section('og_title', config('app.name') . ' · ' . __('Consulta do imóvel'))
@section('og_description', isset($local) ? __('Histórico público de visitas de vigilância em :cidade/:uf — datas e status, sem dados sensíveis.', ['cidade' => $local->loc_cidade, 'uf' => $local->loc_estado]) : __('Resultado da consulta pelo código do imóvel.'))

@section('content')
@php
    $numeroExibicao = $local->loc_numero !== null && $local->loc_numero !== '' ? (string) $local->loc_numero : __('S/N');
    $linhaPopup = $local->loc_endereco.', '.$numeroExibicao;
@endphp
<div class="v-page max-w-5xl space-y-6">
    <x-breadcrumbs :items="[
        ['label' => __('Página Inicial'), 'url' => url('/')],
        ['label' => __('Consulta pública'), 'url' => route('consulta.index')],
        ['label' => __('Cód.') . ' ' . $local->loc_codigo_unico],
    ]" />

    <x-page-header :eyebrow="__('Consulta pública')" :title="__('Visitas registradas neste endereço')">
        <x-slot name="lead">
            <p>{{ __('Os dados abaixo são apenas informativos: localização cadastrada, datas das visitas, tipo de atividade em campo e se havia pendência na época — no mesmo padrão resumido visto na gestão interna, sem identificar o profissional.') }}</p>
            <button type="button" id="btn-baixar-card" aria-label="{{ __('Baixar cartão com QR Code para colar no imóvel') }}"
                    class="v-btn-secondary mt-4 inline-flex items-center gap-2 px-4 py-2.5 text-[13px] font-semibold">
                <x-heroicon-o-arrow-down-tray class="h-4 w-4 shrink-0 text-blue-600 dark:text-blue-400" aria-hidden="true" />
                {{ __('Baixar cartão com QR Code') }}
            </button>
        </x-slot>
    </x-page-header>

    {{-- Card QR Code (oculto, usado para download) --}}
    <div id="adesivo" class="fixed left-[-9999px] top-0 w-[300px] bg-white p-6 text-center">
        <p class="text-[11px] font-semibold text-gray-500 uppercase tracking-widest mb-3">{{ config('app.name') }} · {{ __('Consulta pública') }}</p>
        <p class="text-sm text-gray-800 leading-snug mb-4">
            {{ $local->loc_endereco }}, {{ $numeroExibicao }}<br>
            <span class="text-gray-600">{{ $local->loc_bairro }} · {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
        </p>
        <div class="flex justify-center">
            <img src="data:{{ $qrCodeMime ?? 'image/png' }};base64,{{ $qrCodeBase64 }}" alt="{{ __('QR Code — consulta pública deste imóvel') }}" class="w-32 h-32 block" width="128" height="128">
        </div>
        <p class="text-[10px] text-gray-500 break-all mt-4 font-mono leading-tight px-1">{{ route('consulta.codigo', ['codigo' => $local->loc_codigo_unico]) }}</p>
        <p class="text-[9px] text-gray-400 mt-5">{{ __('Bitwise Technologies') }}</p>
    </div>

    {{-- Imóvel consultado --}}
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 md:items-start">
        <section class="v-card">
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
        </section>
        <section class="v-card" aria-label="{{ __('Mapa do imóvel') }}">
            <h2 class="v-section-title mb-3">{{ __('Localização no mapa') }}</h2>
            <div class="relative h-64 w-full overflow-hidden" id="mapa-local" role="region" aria-label="{{ __('Mapa interativo') }}"></div>
        </section>
    </div>

    {{-- Histórico de visitas (colunas alinhadas à listagem interna, sem profissional) --}}
    <section class="v-card v-card--flush overflow-hidden">
        <div class="p-4 sm:p-5">
            <h2 class="v-section-title">{{ __('Histórico de visitas') }}</h2>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __('Lista ordenada da mais recente para a mais antiga. “Pendente” e “Concluída” usam o mesmo critério da gestão interna; a coluna atividade reproduz o registro de campo.') }}</p>
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
    </section>

    {{-- Resumos para o cidadão (texto neutro, sem dados sensíveis) --}}
    @if (!empty($resumos) && count($resumos) > 0)
    <section class="v-card">
        <h2 class="v-section-title mb-2">{{ __('Resumo em linguagem simples') }}</h2>
        <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">
            {{ __('Texto gerado a partir do registro da visita, sem dados pessoais sensíveis. Para dúvidas ou reclamações, procure a Secretaria Municipal de Saúde.') }}
        </p>
        <div class="space-y-4">
            @foreach ($visitas as $visita)
                @if (isset($resumos[$visita->vis_id]))
                <div class="rounded-xl border border-slate-200/90 bg-slate-50/90 p-4 dark:border-slate-600 dark:bg-slate-800/40">
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
    </section>
    @endif

    {{-- Informativo institucional --}}
    <section class="v-card space-y-2 border-amber-200/80 bg-amber-50/90 text-sm text-amber-950 shadow-sm dark:border-amber-800/60 dark:bg-amber-950/30 dark:text-amber-100">
        <h2 class="text-sm font-semibold text-amber-950 dark:text-amber-100">{{ __('Limites desta consulta') }}</h2>
        <p class="leading-relaxed">
            {{ __('A consulta pública mostra somente o que está vinculado ao código do imóvel: endereço cadastrado, datas de visita e status (pendente ou concluída). Não exibe diagnósticos, nomes de moradores ou outros dados protegidos.') }}
        </p>
        <p class="leading-relaxed">
            {{ __('Para orientação sanitária, segunda via de documentos ou reclamações, fale com a Secretaria Municipal de Saúde do seu município.') }}
        </p>
    </section>

    {{-- Rodapé --}}
    <div class="flex flex-col items-center gap-3 border-t border-slate-200/80 pt-8 dark:border-slate-700/80">
        <a href="{{ route('consulta.index') }}"
           class="v-btn-primary inline-flex items-center justify-center gap-2 px-6 py-2.5 text-[13px] font-semibold">
            <x-heroicon-o-magnifying-glass class="h-4 w-4 shrink-0" aria-hidden="true" />
            {{ __('Consultar outro código') }}
        </a>
        <button type="button" id="btn-compartilhar" aria-label="{{ __('Copiar ou compartilhar o link desta página') }}"
                data-url="{{ url()->current() }}"
                data-title="{{ config('app.name') }} · {{ __('Consulta do imóvel') }}"
                class="v-btn-secondary inline-flex items-center gap-2 px-5 py-2.5 text-[13px] font-semibold">
            <x-heroicon-o-share class="h-4 w-4 shrink-0" aria-hidden="true" />
            <span id="btn-copiar-texto">{{ __('Copiar ou compartilhar link') }}</span>
        </button>
    </div>

</div>

@endsection

@push('scripts')
<script type="application/json" id="consulta-publica-config">
@json([
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
])
</script>
@vite(['resources/js/consulta-codigo.js'])
@endpush
