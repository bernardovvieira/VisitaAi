<!-- resources/views/gestor/dashboard.blade.php -->
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

    $card = 'rounded-xl border border-slate-200/90 bg-white p-5 shadow-sm dark:border-slate-600 dark:bg-slate-800/80';
    $actionBase = 'flex items-center justify-center gap-1.5 rounded-lg border px-3 py-2 text-xs font-semibold shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/35 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900';
@endphp

<div class="mx-auto max-w-7xl space-y-8">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="border-b border-slate-200/90 pb-6 dark:border-slate-700">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 sm:text-[1.65rem]">{{ __('Painel do Gestor') }}</h1>
        <p class="mt-2 text-sm font-medium text-slate-600 dark:text-slate-400">{{ Auth::user()->use_nome }}</p>
    </header>

    <section class="space-y-3" aria-labelledby="heading-stats">
        <h2 id="heading-stats" class="sr-only">{{ __('Estatísticas principais') }}</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Profissionais ACE/ACS aprovados') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-slate-100">{{ \App\Models\User::where(function ($query) { $query->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', true)->count() }}</p>
                    </div>
                    <x-heroicon-o-user-group class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }} {{ $pendentesCount > 0 ? 'border-amber-300/80 ring-1 ring-amber-200 dark:border-amber-600 dark:ring-amber-900/40' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Cadastros pendentes') }}</p>
                            @if($pendentesCount > 0)
                                <span class="rounded-md bg-amber-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-amber-900 dark:bg-amber-900/50 dark:text-amber-200">{{ __('Ação') }}</span>
                            @endif
                        </div>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ $pendentesCount }}</p>
                    </div>
                    <x-heroicon-o-exclamation-triangle class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Gestores') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('use_perfil', 'gestor')->count() }}</p>
                    </div>
                    <x-heroicon-o-user-circle class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Doenças monitoradas') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Doenca::count() }}</p>
                    </div>
                    <x-heroicon-o-beaker class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Visitas registradas') }}</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Visita::count() }}</p>
                    </div>
                    <x-heroicon-o-clipboard-document-list class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }} {{ $visitasComPendencia > 0 ? 'border-red-300/80 ring-1 ring-red-100 dark:border-red-700 dark:ring-red-900/30' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Visitas com pendência') }}</p>
                            @if($visitasComPendencia > 0)
                                <span class="rounded-md bg-red-100 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-red-900 dark:bg-red-900/40 dark:text-red-200">{{ __('Pendente') }}</span>
                            @endif
                        </div>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ $visitasComPendencia }}</p>
                    </div>
                    <x-heroicon-o-clock class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>

            <div class="{{ $card }} sm:col-span-2 lg:col-span-3">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 pb-3 dark:border-gray-700/80">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_titulo') }}</p>
                        <p class="mt-1 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ $totalOcupantesVisitaAi }}</p>
                        @if(config('visitaai_municipio.ocupantes.painel_gestor_subtitulo'))
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_subtitulo') }}</p>
                        @endif
                    </div>
                    <x-heroicon-o-users class="h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
                @if($ocupantesPorBairroTop->isNotEmpty())
                    <p class="mt-3 text-[10px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500">{{ config('visitaai_municipio.ocupantes.painel_gestor_bairros') }}</p>
                    <ul class="mt-2 grid grid-cols-1 gap-1.5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($ocupantesPorBairroTop as $row)
                            <li class="flex justify-between gap-2 border-b border-gray-100 py-2 last:border-0 dark:border-gray-700/80">
                                <span class="min-w-0 truncate font-medium text-gray-800 dark:text-gray-200" title="{{ $row->bairro }}">{{ $row->bairro ?: '-' }}</span>
                                <span class="shrink-0 tabular-nums font-semibold text-gray-900 dark:text-gray-100">{{ $row->total_moradores }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </section>

    @if ($doencaMaisMes && $doencaMaisMes->total > 0)
        <section class="{{ $card }}" aria-labelledby="heading-month">
            <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Monitoramento no mês') }}</p>
            <h3 id="heading-month" class="mt-1 text-xl font-semibold tracking-tight text-gray-900 dark:text-gray-100" title="{{ $doencaMaisMes->doe_nome }}">{{ $doencaMaisMes->doe_nome }}</h3>
            <p class="mt-0.5 text-sm tabular-nums text-gray-600 dark:text-gray-400">{{ (int) $doencaMaisMes->total }} {{ (int) $doencaMaisMes->total === 1 ? __('registro') : __('registros') }}</p>
        </section>
    @endif

    <section class="space-y-3" aria-labelledby="heading-quick">
        <h2 id="heading-quick" class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ __('Ações rápidas') }}</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('gestor.pendentes') }}"
               class="{{ $actionBase }} {{ $pendentesCount > 0 ? 'border-amber-300 bg-amber-50 text-amber-950 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100 dark:hover:bg-amber-900/40' : 'border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700' }}">
                <x-heroicon-o-arrow-right class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ __('Usuários pendentes') }}
                @if($pendentesCount > 0)
                    <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-500 px-1 text-xs font-bold text-white">{{ $pendentesCount }}</span>
                @endif
            </a>
            <a href="{{ route('gestor.users.index') }}" class="{{ $actionBase }} border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-users class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ __('Gerenciar usuários') }}
            </a>
            <a href="{{ route('gestor.doencas.index') }}" class="{{ $actionBase }} border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-beaker class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ __('Doenças monitoradas') }}
            </a>
            <a href="{{ route('gestor.visitas.index') }}" class="{{ $actionBase }} border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-clipboard-document-list class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ __('Visitas realizadas') }}
            </a>
            <a href="{{ route('gestor.indicadores.ocupantes') }}" class="{{ $actionBase }} border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-chart-bar class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ config('visitaai_municipio.indicadores.menu', __('Indicadores municipais')) }}
            </a>
            <a href="{{ route('gestor.relatorios.index') }}" class="{{ $actionBase }} border-slate-200 bg-white text-slate-900 hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700">
                <x-heroicon-o-document-text class="h-4 w-4 shrink-0 opacity-80" aria-hidden="true" />
                {{ __('Relatórios') }}
            </a>
        </div>
    </section>
</div>
@endsection
