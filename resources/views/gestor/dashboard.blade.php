@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Painel do gestor'))
@section('og_description', __('Painel do gestor municipal. Acompanhe estatísticas e gerencie doenças, locais, visitas, usuários e relatórios.'))

@section('content')
@php
    $primeiroNome = strtok((string) Auth::user()->use_nome, ' ') ?: Auth::user()->use_nome;
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

<div class="v-dash">
    <x-breadcrumbs :items="[['label' => __('Página Inicial')]]" />

    <header class="v-dash-header">
        <div class="v-dash-header-text">
            <p class="v-dash-eyebrow">{{ __('Console municipal') }}</p>
            <h1 class="v-dash-title">{{ __('Olá, :nome', ['nome' => $primeiroNome]) }}</h1>
            <p class="v-dash-sub">{{ __('Visão geral do município, indicadores e atalhos para o dia a dia.') }}</p>
        </div>
    </header>

    @if($pendentesCount > 0 || $visitasComPendencia > 0)
        <div class="v-dash-alert-stack" role="region" aria-label="{{ __('Requer atenção') }}">
            @if($pendentesCount > 0)
                <a href="{{ route('gestor.pendentes') }}" class="v-dash-alert v-dash-alert--success">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0 opacity-90" aria-hidden="true" />
                    <span>{{ __(':n cadastro(s) de campo aguardando aprovação', ['n' => $pendentesCount]) }}</span>
                    <x-heroicon-o-arrow-right class="ml-auto h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                </a>
            @endif
            @if($visitasComPendencia > 0)
                <a href="{{ route('gestor.visitas.index', ['busca' => 'pendentes']) }}" class="v-dash-alert v-dash-alert--danger">
                    <x-heroicon-o-clock class="h-5 w-5 shrink-0 opacity-90" aria-hidden="true" />
                    <span>{{ __(':n visita(s) com pendência aberta', ['n' => $visitasComPendencia]) }}</span>
                    <x-heroicon-o-arrow-right class="ml-auto h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                </a>
            @endif
        </div>
    @endif

    <section class="v-dash-card" aria-labelledby="gestor-kpis-heading">
        <h2 id="gestor-kpis-heading" class="v-dash-card__title">{{ __('Indicadores') }}</h2>
        <p class="v-dash-card__sub">{{ __('Números consolidados do município.') }}</p>
        <div class="v-kpi-grid-agi mt-4">
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Profissionais de campo') }}</span>
                <span class="v-kpi-card-agi__value">{{ $profissionaisAprovados }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Aprovados no sistema') }}</span>
            </div>
            <div class="v-kpi-card-agi {{ $pendentesCount > 0 ? 'v-kpi-card-agi--warn' : '' }}">
                <span class="v-kpi-card-agi__label">{{ __('Cadastros pendentes') }}</span>
                <span class="v-kpi-card-agi__value">{{ $pendentesCount }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('ACE/ACS sem aprovação') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Gestores') }}</span>
                <span class="v-kpi-card-agi__value">{{ $gestoresCount }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Doenças monitoradas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $doencasCount }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Visitas registradas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $visitasCount }}</span>
            </div>
            <div class="v-kpi-card-agi {{ $visitasComPendencia > 0 ? 'v-kpi-card-agi--danger' : '' }}">
                <span class="v-kpi-card-agi__label">{{ __('Visitas com pendência') }}</span>
                <span class="v-kpi-card-agi__value">{{ $visitasComPendencia }}</span>
            </div>
        </div>
    </section>

    <div class="v-dash-row">
        <section class="v-dash-card min-h-0 min-w-0 lg:h-full" aria-labelledby="gestor-ocupantes-heading">
            <div class="v-dash-card__head">
                <div>
                    <h2 id="gestor-ocupantes-heading" class="v-dash-card__title">{{ config('visitaai_municipio.ocupantes.painel_gestor_titulo') }}</h2>
                    @if(filled(config('visitaai_municipio.ocupantes.painel_gestor_subtitulo')))
                        <p class="v-dash-card__sub mt-1">{{ config('visitaai_municipio.ocupantes.painel_gestor_subtitulo') }}</p>
                    @endif
                </div>
                <p class="text-2xl font-extrabold tabular-nums tracking-tight text-slate-900 dark:text-slate-50">{{ $totalOcupantesVisitaAi }}</p>
            </div>
            @if($ocupantesPorBairroTop->isNotEmpty())
                <p class="v-toolbar-label mb-2">{{ config('visitaai_municipio.ocupantes.painel_gestor_bairros') }}</p>
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
                                    <td class="font-medium" title="{{ $row->bairro }}">{{ $row->bairro ?: __('N/D') }}</td>
                                    <td class="text-right tabular-nums font-semibold">{{ $row->total_moradores }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="mt-auto flex justify-center pt-4">
                    <a href="{{ route('gestor.indicadores.ocupantes') }}" class="v-btn-primary inline-flex items-center justify-center gap-2 px-5 py-2.5 text-[13px] font-semibold no-underline">{{ __('Ver painel completo de indicadores') }}</a>
                </p>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Ainda não há ocupantes registrados por bairro.') }}</p>
            @endif
        </section>

        <div class="flex min-h-0 flex-col gap-4 lg:h-full">
            @if ($doencaMaisMes && $doencaMaisMes->total > 0)
                <section class="v-dash-card shrink-0 border-l-4 border-l-blue-500/85" aria-labelledby="gestor-monitor-heading">
                    <h2 id="gestor-monitor-heading" class="v-dash-card__title">{{ __('Monitoramento no mês corrente') }}</h2>
                    <p class="mt-2 text-lg font-semibold leading-snug text-slate-900 dark:text-slate-100" title="{{ $doencaMaisMes->doe_nome }}">{{ $doencaMaisMes->doe_nome }}</p>
                    <p class="mt-1 text-sm tabular-nums text-slate-600 dark:text-slate-400">{{ (int) $doencaMaisMes->total }} {{ (int) $doencaMaisMes->total === 1 ? __('registro em monitoradas') : __('registros em monitoradas') }}</p>
                </section>
            @endif

            <section class="v-dash-card flex min-h-0 min-w-0 flex-1 flex-col" aria-labelledby="gestor-atalhos-heading">
                <h2 id="gestor-atalhos-heading" class="v-dash-card__title">{{ __('Ações rápidas') }}</h2>
                <div class="v-dash-shortcuts v-dash-shortcuts--tight mt-4 min-h-0 flex-1 content-start">
                    <a href="{{ route('gestor.pendentes') }}" class="v-dash-shortcut {{ $pendentesCount > 0 ? 'v-dash-shortcut--primary' : '' }}">
                        <x-heroicon-o-exclamation-triangle class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Usuários pendentes') }}</span>
                        </span>
                        @if($pendentesCount > 0)
                            <span class="inline-flex h-6 min-w-[1.5rem] items-center justify-center rounded-full bg-white/20 px-1.5 text-xs font-bold tabular-nums">{{ $pendentesCount }}</span>
                        @endif
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.users.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-users class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Gerenciar usuários') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.visitas.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-clipboard-document-list class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Visitas realizadas') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.locais.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-map-pin class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Locais') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.doencas.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-beaker class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Doenças monitoradas') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.indicadores.ocupantes') }}" class="v-dash-shortcut">
                        <x-heroicon-o-chart-bar class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Indicadores') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.relatorios.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-document-text class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Relatórios') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.logs.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-clipboard-document-check class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Auditoria') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
