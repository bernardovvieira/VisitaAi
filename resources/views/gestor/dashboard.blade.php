@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Painel do gestor'))
@section('og_description', __('Painel do gestor municipal com visão consolidada da operação territorial, indicadores e relatórios.'))

@section('content')
@php
    $primeiroNome = strtok((string) Auth::user()->use_nome, ' ') ?: Auth::user()->use_nome;
    $pendentesCount = \App\Models\User::where(function ($q) { $q->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', false)->count();
    $visitasComPendencia = \App\Models\Visita::where('vis_pendencias', true)->count();
    $ocupantesResumo = app(\App\Services\Municipio\ResumoOcupantesMunicipioService::class);
    $totalOcupantesVisitaAi = $ocupantesResumo->totalOcupantesRegistrados();
    $ocupantesPorBairroTop = $ocupantesResumo->totaisPorBairro()->take(6);
    $profissionaisAprovados = \App\Models\User::where(function ($query) { $query->where('use_perfil', 'agente_endemias')->orWhere('use_perfil', 'agente_saude'); })->where('use_aprovado', true)->count();
    $gestoresCount = \App\Models\User::where('use_perfil', 'gestor')->count();
    $visitasCount = \App\Models\Visita::count();

    $totalImoveis = \App\Models\Local::count();
    $imoveisComOcupante = \App\Models\Local::whereHas('moradores')->count();
    $imoveisComFichaSocio = \App\Models\LocalSocioeconomico::count();
    $habitantesCadastrados = \App\Models\Morador::count();
    $mediaOcupantesPorImovel = $imoveisComOcupante > 0
        ? round($habitantesCadastrados / $imoveisComOcupante, 1)
        : 0;
    $bairrosCobertos = \App\Models\Local::query()
        ->whereNotNull('loc_bairro')
        ->whereRaw("TRIM(loc_bairro) <> ''")
        ->distinct('loc_bairro')
        ->count('loc_bairro');

    $rendaMapSm = [
        'ate_meio_salario' => 0.5,
        'ate_1_sm' => 1.0,
        'ate_2_sm' => 1.5,
        'ate_3_sm' => 2.5,
        'acima_3_sm' => 3.5,
        'acima_5_sm' => 5.5,
    ];
    $rendaRows = \App\Models\LocalSocioeconomico::query()
        ->select('lse_renda_familiar_faixa')
        ->whereNotNull('lse_renda_familiar_faixa')
        ->where('lse_renda_familiar_faixa', '!=', 'nao_informado')
        ->get();
    $rendaSmTotal = 0.0;
    $rendaSmCount = 0;
    foreach ($rendaRows as $row) {
        $faixa = (string) $row->lse_renda_familiar_faixa;
        if (! array_key_exists($faixa, $rendaMapSm)) {
            continue;
        }
        $rendaSmTotal += $rendaMapSm[$faixa];
        $rendaSmCount++;
    }
    $rendaMediaSm = $rendaSmCount > 0 ? round($rendaSmTotal / $rendaSmCount, 2) : null;

    $populacaoReferencia = \App\Models\LocalSocioeconomico::query()
        ->whereNotNull('lse_n_moradores_declarado')
        ->sum('lse_n_moradores_declarado');
@endphp

<div class="v-dash">
    <x-breadcrumbs :items="[['label' => __('Página Inicial')]]" />

    <header class="v-dash-header">
        <div class="v-dash-header-text">
            <p class="v-dash-eyebrow">{{ __('Painel municipal') }}</p>
            <h1 class="v-dash-title">{{ __('Olá, :nome', ['nome' => $primeiroNome]) }}</h1>
            <p class="v-dash-sub">{{ __('Visão geral do município com indicadores e atalhos para a rotina da gestão.') }}</p>
        </div>
    </header>

    @if($pendentesCount > 0 || $visitasComPendencia > 0)
        <div class="v-dash-alert-stack" role="region" aria-label="{{ __('Requer atenção') }}">
            @if($pendentesCount > 0)
                <a href="{{ route('gestor.pendentes') }}" class="v-dash-alert v-dash-alert--warn">
                    <x-heroicon-o-exclamation-triangle class="h-5 w-5 shrink-0 opacity-90" aria-hidden="true" />
                    <span>{{ __('gestor.dashboard.pending_field_staff', ['n' => $pendentesCount]) }}</span>
                    <x-heroicon-o-arrow-right class="ml-auto h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                </a>
            @endif
            @if($visitasComPendencia > 0)
                <a href="{{ route('gestor.visitas.index', ['busca' => 'pendentes']) }}" class="v-dash-alert v-dash-alert--danger">
                    <x-heroicon-o-clock class="h-5 w-5 shrink-0 opacity-90" aria-hidden="true" />
                    <span>{{ $visitasComPendencia }} {{ $visitasComPendencia === 1 ? __('visita com pendência aberta') : __('visitas com pendência aberta') }}</span>
                    <x-heroicon-o-arrow-right class="ml-auto h-4 w-4 shrink-0 opacity-70" aria-hidden="true" />
                </a>
            @endif
        </div>
    @endif

    <section class="v-dash-card" aria-labelledby="gestor-kpis-heading">
        <h2 id="gestor-kpis-heading" class="v-dash-card__title">{{ __('Indicadores') }}</h2>
        <p class="v-dash-card__sub">{{ __('Números consolidados do município com base nos cadastros do sistema.') }}</p>
        <div class="v-kpi-grid-agi mt-4">
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Imóveis cadastrados') }}</span>
                <span class="v-kpi-card-agi__value">{{ $totalImoveis }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Base territorial municipal') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Habitantes cadastrados') }}</span>
                <span class="v-kpi-card-agi__value">{{ $habitantesCadastrados }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Total de ocupantes registrados') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Média de ocupantes/imóvel') }}</span>
                <span class="v-kpi-card-agi__value">{{ number_format($mediaOcupantesPorImovel, 1, ',', '.') }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Somente imóveis com ocupantes') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Bairros cobertos') }}</span>
                <span class="v-kpi-card-agi__value">{{ $bairrosCobertos }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Com imóveis cadastrados') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Renda familiar média estimada') }}</span>
                <span class="v-kpi-card-agi__value">{{ $rendaMediaSm !== null ? number_format($rendaMediaSm, 2, ',', '.') . ' SM' : __('N/D') }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Calculada por faixa de renda informada') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('População declarada nas fichas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $populacaoReferencia > 0 ? $populacaoReferencia : __('N/D') }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Soma do nº de moradores declarado') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Imóveis com ficha socioeconômica') }}</span>
                <span class="v-kpi-card-agi__value">{{ $imoveisComFichaSocio }}</span>
                <span class="v-kpi-card-agi__hint">{{ $totalImoveis > 0 ? number_format(($imoveisComFichaSocio / $totalImoveis) * 100, 0, ',', '.') . '% ' . __('do total') : __('Sem base') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Profissionais de campo') }}</span>
                <span class="v-kpi-card-agi__value">{{ $profissionaisAprovados }}</span>
                <span class="v-kpi-card-agi__hint">{{ __('Aprovados no sistema') }}</span>
            </div>
            <div class="v-kpi-card-agi">
                <span class="v-kpi-card-agi__label">{{ __('Visitas registradas') }}</span>
                <span class="v-kpi-card-agi__value">{{ $visitasCount }}</span>
            </div>
        </div>
        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">
            {{ __('Indicadores demográficos e renda são estimativas internas a partir dos dados cadastrados no Visita Aí.') }}
        </p>
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
                    <a href="{{ route('gestor.indicadores.ocupantes') }}" class="text-sm font-medium text-blue-600 underline decoration-blue-500/50 underline-offset-2 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">{{ __('Ver painel completo de indicadores') }}</a>
                </p>
            @else
                <p class="text-sm text-slate-500 dark:text-slate-400">{{ __('Ainda não há ocupantes registrados por bairro.') }}</p>
            @endif
        </section>

        <div class="flex min-h-0 flex-col lg:h-full">
            <section class="v-dash-card flex h-full min-h-0 min-w-0 flex-1 flex-col" aria-labelledby="gestor-atalhos-heading">
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
                    <a href="{{ route('gestor.locais.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-map-pin class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Locais') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.indicadores.ocupantes') }}" class="v-dash-shortcut">
                        <x-heroicon-o-chart-bar class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Indicadores municipais') }}</span>
                        </span>
                        <x-heroicon-o-chevron-right class="v-dash-shortcut__chevron h-4 w-4" aria-hidden="true" />
                    </a>
                    <a href="{{ route('gestor.visitas.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-clipboard-document-list class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Visitas de campo') }}</span>
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
                    <a href="{{ route('gestor.relatorios.index') }}" class="v-dash-shortcut">
                        <x-heroicon-o-document-text class="v-dash-shortcut__icon h-5 w-5" aria-hidden="true" />
                        <span class="v-dash-shortcut__body">
                            <span class="v-dash-shortcut__label">{{ __('Relatórios operacionais') }}</span>
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
