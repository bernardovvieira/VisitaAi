@extends('layouts.app')

@section('og_title', config('app.name') . ' · Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_saude'))
@section('og_description', 'Painel do ' . \App\Helpers\MsTerminologia::perfilLabel('agente_saude') . '. Registre visitas LIRAa e consulte doenças monitoradas. Conforme Lei 11.350/2006 e Diretriz MS.')

@section('content')
@php
    $card = 'rounded-xl border border-slate-200/90 bg-white p-4 shadow-sm dark:border-slate-600 dark:bg-slate-800/80';
    $actionBase = 'flex items-center justify-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-100 dark:hover:bg-slate-700';
@endphp
<div class="mx-auto max-w-7xl space-y-8">
    <x-breadcrumbs :items="[['label' => 'Página Inicial']]" />

    <header class="border-b border-slate-200/90 pb-6 dark:border-slate-700">
        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 sm:text-[1.65rem]">Painel do {{ \App\Helpers\MsTerminologia::perfilLabel('agente_saude') }}</h1>
        <p class="mt-2 text-sm font-medium text-slate-600 dark:text-slate-400">
            {{ Auth::user()->use_nome }}
            <span class="mx-1.5 text-slate-300 dark:text-slate-600">·</span>
            <span class="font-semibold text-slate-800 dark:text-slate-200">LIRAa</span>
        </p>
    </header>

    <section class="space-y-3" aria-labelledby="heading-stats-saude">
        <h2 id="heading-stats-saude" class="sr-only">Estatísticas</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Visitas LIRAa (suas)</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-slate-100">{{ \App\Models\Visita::where('fk_usuario_id', Auth::user()->use_id)->where('vis_atividade', '7')->count() }}</p>
                    </div>
                    <x-heroicon-o-clipboard-document-list class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>
            <div class="{{ $card }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0 flex-1">
                        <p class="text-[11px] font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Doenças monitoradas</p>
                        <p class="mt-2 text-3xl font-semibold tabular-nums tracking-tight text-slate-900 dark:text-slate-100">{{ \App\Models\Doenca::count() }}</p>
                    </div>
                    <x-heroicon-o-beaker class="mt-0.5 h-5 w-5 shrink-0 text-slate-400 dark:text-slate-500" aria-hidden="true" />
                </div>
            </div>
        </div>
    </section>

    <section class="space-y-3" aria-labelledby="heading-quick-saude">
        <h2 id="heading-quick-saude" class="text-sm font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">Ações rápidas</h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <a href="{{ route('saude.doencas.index') }}" class="{{ $actionBase }}">
                <x-heroicon-o-beaker class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                Doenças
            </a>
            <a href="{{ route('saude.visitas.create') }}" class="{{ $actionBase }}">
                <x-heroicon-o-plus-circle class="h-5 w-5 shrink-0 text-emerald-600 dark:text-emerald-400" aria-hidden="true" />
                Nova visita LIRAa
            </a>
            <a href="{{ route('saude.visitas.index') }}" class="{{ $actionBase }}">
                <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 opacity-80" aria-hidden="true" />
                Minhas visitas
            </a>
        </div>
    </section>

    <section class="border-t border-slate-200/90 pt-6 dark:border-slate-700" aria-label="Suporte">
        <p class="flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
            <span class="font-medium text-slate-600 dark:text-slate-300">Apoio</span>
            <a href="https://bitwise.dev.br" target="_blank" rel="noopener noreferrer" class="font-medium text-emerald-700 underline decoration-emerald-700/30 underline-offset-2 hover:decoration-emerald-700 dark:text-emerald-400">bitwise.dev.br</a>
            <span class="text-slate-300 dark:text-slate-600" aria-hidden="true">·</span>
            <a href="mailto:bernardo@bitwise.dev.br" class="font-medium text-emerald-700 underline decoration-emerald-700/30 underline-offset-2 hover:decoration-emerald-700 dark:text-emerald-400">bernardo@bitwise.dev.br</a>
        </p>
    </section>
</div>
@endsection
