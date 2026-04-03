{{-- resources/views/agente/dashboard.blade.php --}}
@extends('layouts.app')

@section('og_title', config('app.name') . ' — Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_endemias'))
@section('og_description', 'Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_endemias') . '. Registre visitas, gerencie locais e consulte doenças monitoradas. Conforme Lei 11.350/2006 e Diretriz MS.')

@section('content')
@php
    $card = 'rounded-xl border border-gray-200/80 bg-white p-4 shadow-sm dark:border-gray-600 dark:bg-gray-800';
@endphp
<div class="mx-auto max-w-7xl space-y-8">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="flex flex-col gap-2 border-b border-gray-100 pb-6 dark:border-gray-800 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:text-3xl">Painel do {{ \App\Helpers\MsTerminologia::perfilLabel('agente_endemias') }}</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->use_nome }}</p>
        </div>
        <p id="clock" class="text-xs tabular-nums text-gray-400 dark:text-gray-500"></p>
    </header>

    <section class="space-y-3" aria-labelledby="heading-stats-agente">
        <h2 id="heading-stats-agente" class="sr-only">Estatísticas</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Minhas visitas</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Visita::where('fk_usuario_id', Auth::user()->use_id)->count() }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-4.42 0-8 1.79-8 4v2h16v-2c0-2.21-3.58-4-8-4z"/></svg>
                </div>
            </div>
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Visitas (todas)</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Visita::count() }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                </div>
            </div>
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Locais</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Local::count() }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 11c1.657 0 3-1.343 3-3S13.657 5 12 5 9 6.343 9 8s1.343 3 3 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 21C12 21 5 14.5 5 9a7 7 0 0114 0c0 5.5-7 12-7 12z"/></svg>
                </div>
            </div>
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Doenças</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-gray-900 dark:text-gray-100">{{ \App\Models\Doenca::count() }}</p>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 h-5 w-5 shrink-0 text-gray-400 dark:text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M9 17v-6m4 6v-10m4 10v-4"/></svg>
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-3" aria-labelledby="heading-quick-agente">
        <h2 id="heading-quick-agente" class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Ações rápidas</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('agente.visitas.create') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Registrar Visita
            </a>
            <a href="{{ route('agente.visitas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Visitas Realizadas
            </a>
            <a href="{{ route('agente.locais.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Locais Cadastrados
            </a>
            <a href="{{ route('agente.doencas.index') }}" class="flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 rounded shadow-sm hover:shadow-lg transition dark:hover:bg-gray-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-500 dark:text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                Consultar Doenças
            </a>
        </div>
    </section>

    <section class="border-t border-gray-100 pt-6 dark:border-gray-800" aria-label="Suporte">
        <p class="text-xs text-gray-500 dark:text-gray-400">
            Apoio —
            <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-700 underline decoration-blue-700/30 underline-offset-2 hover:decoration-blue-700 dark:text-blue-400">bitwise.dev.br</a>
            ·
            <a href="mailto:bernardo@bitwise.dev.br" class="font-medium text-blue-700 underline decoration-blue-700/30 underline-offset-2 hover:decoration-blue-700 dark:text-blue-400">bernardo@bitwise.dev.br</a>
        </p>
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
