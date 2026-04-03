@extends('layouts.app')

@section('og_title', config('app.name') . ' · Painel do Gestor')
@section('og_description', 'Painel do gestor municipal. Acompanhe estatísticas e gerencie doenças, locais, visitas, usuários e relatórios.')

@section('content')
@php
    $pendentesCount = \App\Models\User::where(function ($q) { $q->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', false)->count();
    $visitasComPendencia = \App\Models\Visita::where('vis_pendencias', true)->count();
    $ocupantesResumo = app(\App\Services\Municipio\ResumoOcupantesMunicipioService::class);
    $totalOcupantesVisitaAi = $ocupantesResumo->totalOcupantesRegistrados();
    $ocupantesPorBairroTop = $ocupantesResumo->totaisPorBairro()->take(6);
    $inicioMes = now()->startOfMonth();
    $doencaMaisMes = \Illuminate\Support\Facades\DB::table('monitoradas')
        ->join('visitas', 'visitas.vis_id', '=', 'monitoradas.fk_visita_id')
        ->join('doencas', 'doencas.doe_id', '=', 'monitoradas.fk_doenca_id')
        ->where('visitas.vis_data', '>=', $inicioMes)
        ->select('doencas.doe_nome', \Illuminate\Support\Facades\DB::raw('COUNT(*) as total'))
        ->groupBy('doencas.doe_id', 'doencas.doe_nome')
        ->orderByDesc('total')
        ->first();
    $profissionaisAprovados = \App\Models\User::where(function ($query) { $query->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', true)->count();
    $gestoresCount = \App\Models\User::where('use_perfil', 'gestor')->count();
    $doencasCount = \App\Models\Doenca::count();
    $visitasCount = \App\Models\Visita::count();
@endphp

<div class="v-page">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="v-page-header">
        <h1 class="v-page-title">{{ __('Painel do gestor') }}</h1>
        <p class="v-page-lead">
            {{ __('Olá, :nome. Visão geral do município e atalhos para o dia a dia.', ['nome' => Auth::user()->use_nome]) }}
            <span class="mt-1 block text-xs text-slate-500 dark:text-slate-500">{{ now()->translatedFormat('l, j \d\e F \d\e Y') }}</span>
        </p>
    </header>

    <div class="v-panel">
        @if($pendentesCount > 0 || $visitasComPendencia > 0)
            <div class="border-b border-slate-100 bg-gradient-to-r from-slate-50 to-white p-4 dark:border-slate-700 dark:from-slate-800/40 dark:to-slate-900/30 sm:p-5">
                <p class="v-toolbar-label mb-2">{{ __('Requer atenção') }}</p>
                <ul class="flex flex-col gap-2 sm:flex-row sm:flex-wrap">
                    @if($pendentesCount > 0)
                        <li>
                            <a href="{{ route('gestor.pendentes') }}" class="inline-flex items-center gap-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-950 transition hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-950/35 dark:text-amber-100 dark:hover:bg-amber-950/55">
                                <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ __(':n cadastro(s) de campo aguardando aprovação', ['n' => $pendentesCount]) }}
                                <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                            </a>
                        </li>
                    @endif
                    @if($visitasComPendencia > 0)
                        <li>
                            <a href="{{ route('gestor.visitas.index', ['busca' => 'pendentes']) }}" class="inline-flex items-center gap-2 rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-950 transition hover:bg-rose-100 dark:border-rose-900 dark:bg-rose-950/35 dark:text-rose-100 dark:hover:bg-rose-950/50">
                                <x-heroicon-o-clock class="h-5 w-5 shrink-0" aria-hidden="true" />
                                {{ __(':n visita(s) com pendência aberta', ['n' => $visitasComPendencia]) }}
                                <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
        @endif

        <div class="v-panel-section">
            <h2 class="v-toolbar-label mb-3">{{ __('Indicadores') }}</h2>
            <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-user-group class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Profissionais de campo') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $profissionaisAprovados }}</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ __('Aprovados no sistema') }}</p>
                    </div>
                </div>

                <div class="v-dashboard-kpi {{ $pendentesCount > 0 ? 'border-amber-300/70 ring-1 ring-amber-200/80 dark:border-amber-700 dark:ring-amber-900/40' : '' }}">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--amber" aria-hidden="true">
                        <x-heroicon-o-exclamation-triangle class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Cadastros pendentes') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $pendentesCount }}</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ __('ACE/ACS sem aprovação') }}</p>
                    </div>
                </div>

                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-user-circle class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Gestores') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $gestoresCount }}</p>
                    </div>
                </div>

                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-beaker class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Doenças monitoradas') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $doencasCount }}</p>
                    </div>
                </div>

                <div class="v-dashboard-kpi">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--blue" aria-hidden="true">
                        <x-heroicon-o-clipboard-document-list class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Visitas registradas') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $visitasCount }}</p>
                    </div>
                </div>

                <div class="v-dashboard-kpi {{ $visitasComPendencia > 0 ? 'border-rose-300/70 ring-1 ring-rose-200/80 dark:border-rose-800 dark:ring-rose-900/35' : '' }}">
                    <div class="v-dashboard-kpi__icon v-dashboard-kpi__icon--rose" aria-hidden="true">
                        <x-heroicon-o-clock class="h-6 w-6 shrink-0" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Visitas com pendência') }}</p>
                        <p class="mt-1 text-2xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $visitasComPendencia }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="v-panel-section-muted">
            <div class="flex flex-col gap-1 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ config('visitaai_municipio.ocupantes.painel_gestor_titulo') }}</h2>
                    @if(filled(config('visitaai_municipio.ocupantes.painel_gestor_subtitulo')))
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_subtitulo') }}</p>
                    @endif
                </div>
                <p class="text-3xl font-bold tabular-nums text-slate-900 dark:text-slate-50">{{ $totalOcupantesVisitaAi }}</p>
            </div>
            @if($ocupantesPorBairroTop->isNotEmpty())
                <p class="v-toolbar-label mt-4 mb-2">{{ config('visitaai_municipio.ocupantes.painel_gestor_bairros') }}</p>
                <div class="v-table-wrap rounded-xl border border-slate-200/80 dark:border-slate-600">
                    <table class="v-data-table">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Bairro / localidade') }}</th>
                                <th scope="col" class="w-28 text-right">{{ __('Ocupantes') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ocupantesPorBairroTop as $row)
                                <tr>
                                    <td class="font-medium" title="{{ $row->bairro }}">{{ $row->bairro ?: '—' }}</td>
                                    <td class="text-right tabular-nums font-semibold">{{ $row->total_moradores }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-3 text-center">
                    <a href="{{ route('gestor.indicadores.ocupantes') }}" class="text-sm font-semibold text-blue-600 hover:underline dark:text-blue-400">{{ __('Ver painel completo de indicadores') }}</a>
                </p>
            @else
                <p class="mt-3 text-sm text-slate-500 dark:text-slate-400">{{ __('Ainda não há ocupantes registrados por bairro.') }}</p>
            @endif
        </div>

        @if ($doencaMaisMes && $doencaMaisMes->total > 0)
            <div class="v-panel-section border-l-4 border-l-blue-500/80">
                <p class="v-toolbar-label">{{ __('Monitoramento no mês corrente') }}</p>
                <p class="mt-1 text-lg font-semibold text-slate-900 dark:text-slate-100" title="{{ $doencaMaisMes->doe_nome }}">{{ $doencaMaisMes->doe_nome }}</p>
                <p class="mt-1 text-sm tabular-nums text-slate-600 dark:text-slate-400">{{ (int) $doencaMaisMes->total }} {{ (int) $doencaMaisMes->total === 1 ? __('registro em monitoradas') : __('registros em monitoradas') }}</p>
            </div>
        @endif

        <div class="v-panel-section">
            <h2 class="v-toolbar-label mb-3">{{ __('Ações rápidas') }}</h2>
            <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                <a href="{{ route('gestor.pendentes') }}" class="v-dashboard-action {{ $pendentesCount > 0 ? 'v-dashboard-action--primary' : '' }}">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Usuários pendentes') }}</span>
                    @if($pendentesCount > 0)
                        <span class="ml-1 inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-white/25 px-1.5 text-xs font-bold tabular-nums">{{ $pendentesCount }}</span>
                    @endif
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('gestor.users.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-users class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Gerenciar usuários') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('gestor.visitas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Visitas realizadas') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('gestor.doencas.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Doenças monitoradas') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('gestor.indicadores.ocupantes') }}" class="v-dashboard-action">
                    <x-heroicon-o-chart-bar class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Indicadores') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
                <a href="{{ route('gestor.relatorios.index') }}" class="v-dashboard-action">
                    <x-heroicon-o-document-text class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" aria-hidden="true" />
                    <span class="min-w-0">{{ __('Relatórios') }}</span>
                    <x-heroicon-o-chevron-right class="v-dashboard-action__chevron h-5 w-5" aria-hidden="true" />
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
