<!-- resources/views/gestor/dashboard.blade.php -->
@extends('layouts.app')

@section('og_title', config('app.name') . ' — Painel do Gestor')
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

    $card = 'rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800';
    $actionBase = 'flex items-center justify-center gap-2 rounded-xl border px-4 py-3 text-sm font-semibold shadow-sm transition focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900';
@endphp

<div class="mx-auto max-w-7xl space-y-10">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="space-y-2">
        <h1 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-gray-100">{{ __('Painel do Gestor') }}</h1>
        <p class="max-w-3xl text-gray-600 dark:text-gray-400">{{ __('Resumo operacional do município no Visita Aí — vigilância entomológica, equipes de campo e complementos cadastrais.') }}</p>
    </header>

    <section class="{{ $card }}">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Bem-vindo(a), :nome', ['nome' => Auth::user()->use_nome]) }}</h2>
        <p class="mt-3 text-sm leading-relaxed text-gray-600 dark:text-gray-400">
            {{ __('O sistema organiza o trabalho dos profissionais ACE e ACS e o registro de visitas, em linha com a Lei 11.350/2006 e as diretrizes do Ministério da Saúde. Utilize os cartões abaixo para acompanhar o que precisa de atenção.') }}
        </p>
        <div class="mt-5 flex items-center gap-2 border-t border-gray-100 pt-4 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l4 2M12 4a8 8 0 100 16 8 8 0 000-16z" />
            </svg>
            <span id="clock" class="tabular-nums"></span>
        </div>
    </section>

    <section class="space-y-4" aria-labelledby="heading-stats">
        <h2 id="heading-stats" class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Estatísticas principais') }}</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="{{ $card }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Profissionais ACE/ACS aprovados') }}</h3>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ \App\Models\User::where(function ($query) { $query->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', true)->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }} {{ $pendentesCount > 0 ? 'border-amber-300 ring-2 ring-amber-200 dark:border-amber-600 dark:ring-amber-900/50' : '' }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0zM12 9v4m0 4h.01"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Cadastros pendentes de aprovação') }}</h3>
                            @if($pendentesCount > 0)
                                <span class="rounded-full bg-amber-100 px-2 py-0.5 text-xs font-semibold text-amber-800 dark:bg-amber-900/60 dark:text-amber-200">{{ __('Requer ação') }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ $pendentesCount }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Gestores no sistema') }}</h3>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ \App\Models\User::where('use_perfil', 'gestor')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/>
                        </svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Doenças monitoradas') }}</h3>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ \App\Models\Doenca::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-slate-700 dark:bg-slate-700/50 dark:text-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Visitas registradas') }}</h3>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ \App\Models\Visita::count() }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }} {{ $visitasComPendencia > 0 ? 'border-red-300 ring-2 ring-red-100 dark:border-red-700 dark:ring-red-900/40' : '' }}">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg {{ $visitasComPendencia > 0 ? 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300' : 'bg-slate-100 text-slate-600 dark:bg-slate-700/50' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Visitas com pendência') }}</h3>
                            @if($visitasComPendencia > 0)
                                <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-semibold text-red-800 dark:bg-red-900/50 dark:text-red-200">{{ __('Atenção') }}</span>
                            @endif
                        </div>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ $visitasComPendencia }}</p>
                    </div>
                </div>
            </div>

            <div class="{{ $card }} sm:col-span-2 lg:col-span-3">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_titulo') }}</h3>
                        <p class="mt-1 text-3xl font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ $totalOcupantesVisitaAi }}</p>
                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_subtitulo') }}</p>
                        @if($ocupantesPorBairroTop->isNotEmpty())
                            <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ config('visitaai_municipio.ocupantes.painel_gestor_bairros') }}</p>
                            <ul class="mt-2 grid grid-cols-1 gap-2 text-sm sm:grid-cols-2 lg:grid-cols-3">
                                @foreach($ocupantesPorBairroTop as $row)
                                    <li class="flex justify-between gap-2 rounded-lg border border-gray-100 bg-gray-50/80 px-3 py-2 dark:border-gray-600 dark:bg-gray-900/40">
                                        <span class="truncate text-gray-700 dark:text-gray-300" title="{{ $row->bairro }}">{{ $row->bairro ?: '—' }}</span>
                                        <span class="shrink-0 font-semibold tabular-nums text-gray-900 dark:text-gray-100">{{ $row->total_moradores }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($doencaMaisMes && $doencaMaisMes->total > 0)
        <section class="{{ $card }}" aria-labelledby="heading-month">
            <h2 id="heading-month" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Destaque do mês (monitoramento)') }}</h2>
            <p class="mt-2 text-2xl font-semibold text-gray-900 dark:text-gray-100" title="{{ $doencaMaisMes->doe_nome }}">{{ $doencaMaisMes->doe_nome }}</p>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ (int) $doencaMaisMes->total }} {{ (int) $doencaMaisMes->total === 1 ? __('registro vinculado') : __('registros vinculados') }}</p>
        </section>
    @endif

    <section class="space-y-4" aria-labelledby="heading-quick">
        <h2 id="heading-quick" class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ __('Ações rápidas') }}</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('gestor.pendentes') }}"
               class="{{ $actionBase }} {{ $pendentesCount > 0 ? 'border-amber-300 bg-amber-50 text-amber-950 hover:bg-amber-100 dark:border-amber-700 dark:bg-amber-950/40 dark:text-amber-100 dark:hover:bg-amber-900/40' : 'border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700' }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                {{ __('Usuários pendentes') }}
                @if($pendentesCount > 0)
                    <span class="inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-amber-500 px-1 text-xs font-bold text-white">{{ $pendentesCount }}</span>
                @endif
            </a>
            <a href="{{ route('gestor.users.index') }}" class="{{ $actionBase }} border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"/></svg>
                {{ __('Gerenciar usuários') }}
            </a>
            <a href="{{ route('gestor.doencas.index') }}" class="{{ $actionBase }} border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                {{ __('Doenças monitoradas') }}
            </a>
            <a href="{{ route('gestor.visitas.index') }}" class="{{ $actionBase }} border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                {{ __('Visitas realizadas') }}
            </a>
            <a href="{{ route('gestor.indicadores.ocupantes') }}" class="{{ $actionBase }} border-blue-200 bg-blue-50 text-blue-950 hover:bg-blue-100 dark:border-blue-800 dark:bg-blue-950/50 dark:text-blue-100 dark:hover:bg-blue-900/40">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                {{ config('visitaai_municipio.indicadores.menu', __('Indicadores municipais')) }}
            </a>
            <a href="{{ route('gestor.relatorios.index') }}" class="{{ $actionBase }} border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('Relatórios') }}
            </a>
            <a href="{{ route('gestor.logs.index') }}" class="{{ $actionBase }} border-gray-200 bg-gray-50 text-gray-900 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 opacity-80" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                {{ __('Auditoria') }}
            </a>
        </div>
    </section>

    <section class="{{ $card }}" aria-labelledby="heading-support">
        <h2 id="heading-support" class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ __('Ajuda e suporte') }}</h2>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">{{ __('Em caso de falha ou dúvida técnica, contacte o apoio.') }}</p>
        <ul class="mt-3 space-y-2 text-sm text-gray-700 dark:text-gray-300">
            <li><span class="font-medium text-gray-900 dark:text-gray-100">{{ __('Site') }}:</span> <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-700 underline decoration-blue-700/30 underline-offset-2 transition hover:decoration-blue-700 dark:text-blue-400">{{ __('bitwise.dev.br') }}</a></li>
            <li><span class="font-medium text-gray-900 dark:text-gray-100">{{ __('E-mail') }}:</span> <a href="mailto:bernardo@bitwise.dev.br" class="font-medium text-blue-700 underline decoration-blue-700/30 underline-offset-2 transition hover:decoration-blue-700 dark:text-blue-400">bernardo@bitwise.dev.br</a></li>
        </ul>
    </section>
</div>

<script>
    function updateClock() {
        const el = document.getElementById('clock');
        if (!el) return;
        const now = new Date();
        el.textContent = now.toLocaleDateString('pt-BR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();
</script>
@endsection
