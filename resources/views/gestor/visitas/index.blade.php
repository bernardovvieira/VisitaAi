@extends('layouts.app')

@section('og_title', config('app.name') . ' · Visitas')
@section('og_description', 'Visitas de vigilância entomológica e controle vetorial registradas. Visualize e busque visitas realizadas pelos profissionais (ACE/ACS).')

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas']]" />

    <header class="v-page-header">
        <h1 class="v-page-title">{{ __('Visitas') }}</h1>
        <p class="v-page-lead">{{ __('Visualize e busque visitas registradas pelos profissionais de campo (ACE e ACS).') }}</p>
    </header>

    @if(session('success'))
        <x-alert type="success" :message="session('success')" />
    @endif
    @if(session('warning'))
        <x-alert type="warning" :message="session('warning')" :autodismiss="false" />
    @endif
    @if(session('error'))
        <x-alert type="error" :message="session('error')" />
    @endif

    <div class="v-panel">
        @if($locaisComPendenciasNaoRevisitadas->isNotEmpty())
            <div class="border-b border-amber-200/80 bg-amber-50/90 p-4 sm:p-5 dark:border-amber-800/60 dark:bg-amber-950/35">
                <h2 class="text-sm font-semibold text-amber-950 dark:text-amber-100">{{ __('Pendências sem revisita') }}</h2>
                <p class="mt-1 text-xs text-amber-900/85 dark:text-amber-200/80">{{ __('Locais com pendência registrada e sem visita posterior.') }}</p>
                <ul class="mt-3 space-y-2 text-sm text-amber-950 dark:text-amber-100">
                    @foreach ($locaisComPendenciasNaoRevisitadas as $local)
                        @php
                            $ultimaPendencia = $local->visitas()->where('vis_pendencias', true)->latest('vis_data')->first();
                        @endphp
                        <li class="flex flex-col gap-0.5 border-l-2 border-amber-400/80 pl-3 sm:flex-row sm:items-baseline sm:justify-between">
                            <span>{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}</span>
                            @if($ultimaPendencia)
                                <span class="text-xs font-medium text-amber-800 dark:text-amber-300">{{ __('Última pendência: :d', ['d' => \Carbon\Carbon::parse($ultimaPendencia->vis_data)->format('d/m/Y')]) }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="v-panel-section-muted">
            <label for="search" class="v-toolbar-label">{{ __('Busca inteligente') }}</label>
            <div class="flex items-center gap-2">
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
                {{ __('Exibindo :atual de :total visita(s).', ['atual' => $visitas->count(), 'total' => $visitas->total()]) }}
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
                                    <span class="inline-flex rounded-md bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-900 dark:bg-blue-950/50 dark:text-blue-200">{{ __('Concluída') }}</span>
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
                                   class="btn-acesso-principal inline-flex h-9 w-9 items-center justify-center rounded-xl shadow-sm transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                                   title="{{ __('Visualizar') }}"
                                   aria-label="{{ __('Visualizar visita') }}">
                                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center">
                                <div class="mx-auto flex max-w-md flex-col items-center">
                                    <div class="mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 dark:bg-slate-800">
                                        <x-heroicon-o-clipboard-document-list class="h-7 w-7 shrink-0 text-slate-400 dark:text-slate-500" />
                                    </div>
                                    <p class="font-medium text-slate-700 dark:text-slate-200">{{ __('Nenhuma visita registrada.') }}</p>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('As visitas aparecem aqui quando ACE ou ACS as registrarem.') }}</p>
                                    <a href="{{ route('gestor.locais.index') }}" class="mt-5 inline-flex items-center rounded-xl bg-slate-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600">
                                        {{ __('Ver locais cadastrados') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="v-panel-section border-t border-slate-100 dark:border-slate-700/80">
            <x-pagination-relatorio :paginator="$visitas->appends(request()->query())" item-label="visitas" />
        </div>
    </div>
</div>
@endsection
