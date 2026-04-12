@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Visitas'))
@section('og_description', __('Visitas registradas pelos profissionais de campo. Acompanhe e consulte os registros operacionais.'))

@section('content')
<div class="v-page v-page--wide v-page--dense">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Visitas')]]" />

    <x-page-header :eyebrow="__('Vigilância em campo')" :title="__('Visitas')">
        <x-slot name="lead">
            <p>{{ __('Acompanhe e busque visitas registradas pelos profissionais de campo (ACE e ACS).') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts />

    @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
        @php
            $locaisPendencias = $locaisComPendenciasNaoRevisitadas->values();
            $pendenciasVisiveis = 3;
        @endphp
        <x-ui.callout variant="amber" :title="__('Pendências sem revisita')" x-data="{ expandedPendencias: false }">
            <p class="mt-1 text-xs text-amber-900/85 dark:text-amber-200/80">{{ __('Locais com pendência registrada sem revisita posterior.') }}</p>
            <ul class="mt-3 space-y-2 text-sm text-amber-950 dark:text-amber-100">
                @foreach ($locaisPendencias as $indice => $local)
                    @php
                        $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                    @endphp
                    <li class="flex flex-col gap-0.5 border-l-2 border-amber-400/80 pl-3 sm:flex-row sm:items-center sm:justify-between"
                        @if($indice >= $pendenciasVisiveis) x-show="expandedPendencias" x-cloak @endif>
                        <span>{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
                        @if($ultimaPendencia)
                            <span class="text-xs font-medium text-amber-800 dark:text-amber-300">{{ __('Última pendência: :d', ['d' => \Carbon\Carbon::parse($ultimaPendencia->vis_data)->format('d/m/Y')]) }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
            @if($locaisPendencias->count() > $pendenciasVisiveis)
                <div class="mt-3 flex justify-end">
                    <button type="button"
                            class="inline-flex items-center gap-2 rounded-lg border border-amber-300/80 bg-white px-3 py-1.5 text-xs font-semibold text-amber-900 shadow-sm transition hover:bg-amber-50 dark:border-amber-700/70 dark:bg-amber-950/30 dark:text-amber-100 dark:hover:bg-amber-950/50"
                            @click="expandedPendencias = !expandedPendencias"
                            x-text="expandedPendencias ? '{{ __('Ver menos') }}' : '{{ __('Ver mais') }}'">
                    </button>
                </div>
            @endif
        </x-ui.callout>
    @endif

        <x-section-card class="v-card--flush overflow-hidden">
        <div class="v-list-toolbar">
            <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
            <div class="mt-1 flex items-center gap-2">
                <input type="text" id="search" name="busca" value="{{ old('busca', request('busca')) }}"
                       data-live-url="{{ route('gestor.visitas.index') }}" data-live-param="busca"
                       data-live-loading-id="search-loading-gestor-visitas"
                       placeholder="{{ __('Local, profissional, doença, atividade, pendentes ou data…') }}"
                       class="v-input" />
                <span id="search-loading-gestor-visitas" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
            </div>
        </div>
        <div class="v-table-meta">
            <span>
                {{ __('Exibindo :atual de :total :item.', ['atual' => $visitas->count(), 'total' => $visitas->total(), 'item' => $visitas->total() === 1 ? __('visita') : __('visitas')]) }}
                @if(request('busca'))
                    <span class="text-slate-500 dark:text-slate-500">{{ __('Filtro:') }} <strong class="text-slate-700 dark:text-slate-300">{{ request('busca') }}</strong></span>
                @endif
            </span>
        </div>

        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col" class="whitespace-nowrap">{{ __('Código') }}</th>
                        <th scope="col">{{ __('Data') }}</th>
                        <th scope="col">{{ __('Pendência') }}</th>
                        <th scope="col">{{ __('Atividade') }}</th>
                        <th scope="col" class="min-w-[12rem]">{{ __('Local') }}</th>
                        <th scope="col" class="align-bottom">
                            <span class="block leading-tight normal-case tracking-normal">{{ __('Profissional') }}</span>
                            <span class="mt-0.5 block text-[10px] font-normal normal-case tracking-normal text-slate-500 dark:text-slate-400">{{ __('ACE ou ACS') }}</span>
                        </th>
                        <th scope="col" class="w-14 text-center">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($visitas as $visita)
                        <tr>
                            <td class="whitespace-nowrap">
                                <span class="inline-flex rounded-lg bg-slate-100 px-2 py-1 text-xs font-semibold tabular-nums text-slate-800 dark:bg-slate-800 dark:text-slate-200">#{{ $visita->vis_id }}</span>
                            </td>
                            <td class="leading-tight">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}</div>
                            </td>
                            <td>
                                @if($visita->vis_pendencias)
                                    <span class="inline-flex rounded-md bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-900 dark:bg-red-950/60 dark:text-red-200">{{ __('Pendente') }}</span>
                                    @php
                                        $revisitaPosterior = $visita->local->visitas()
                                            ->where('vis_data', '>', $visita->vis_data)
                                            ->orderBy('vis_data')
                                            ->first();
                                    @endphp
                                    @if($revisitaPosterior)
                                        <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Revisitado em :d', ['d' => \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y')]) }}</div>
                                    @endif
                                @else
                                    <span class="inline-flex rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-950/45 dark:text-emerald-200">{{ __('Concluída') }}</span>
                                @endif
                            </td>
                            <td class="text-slate-600 dark:text-slate-300" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                                {{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: __('Não informado') }}
                            </td>
                            <td class="leading-snug">
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                                <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ $visita->local->loc_bairro }} · {{ __('Cód.') }} {{ $visita->local->loc_codigo_unico }}
                                    <br>{{ __('Resp.') }} {{ $visita->local->loc_responsavel_nome ?? __('Não informado') }}
                                </div>
                            </td>
                            <td>
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $visita->usuario->use_nome }}</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">{{ \App\Models\User::perfilLabel($visita->usuario->use_perfil) }}</div>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('gestor.visitas.show', $visita) }}"
                                   class="v-btn-icon-primary v-btn-icon-primary--lg"
                                   title="{{ __('Visualizar') }}"
                                   aria-label="{{ __('Visualizar visita') }}">
                                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="!p-0">
                                <div class="v-empty-state px-4 py-10">
                                    <div class="v-empty-state__icon" aria-hidden="true">
                                        <x-heroicon-o-clipboard-document-list class="h-7 w-7 shrink-0" />
                                    </div>
                                    <p class="v-empty-state__title">{{ __('Nenhuma visita registrada.') }}</p>
                                    <p class="v-empty-state__text">{{ __('As visitas aparecem aqui quando ACE ou ACS as registrarem.') }}</p>
                                    <a href="{{ route('gestor.locais.index') }}" class="v-btn-compact v-btn-compact--slate mt-5">
                                        {{ __('Ver locais cadastrados') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-pagination-relatorio :paginator="$visitas->appends(request()->query())" item-label="visitas" />
        </x-section-card>
</div>
@endsection
