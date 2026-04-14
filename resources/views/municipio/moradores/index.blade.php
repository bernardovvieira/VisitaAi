@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . config('visitaai_municipio.ocupantes.titulo_listagem'))
@section('og_description', __('Lista de ocupantes do imóvel para uso municipal e indicadores agregados.'))

@section('content')
<div class="v-page">
    <x-breadcrumbs :items="array_filter([
        ['label' => __('Página Inicial'), 'url' => route('dashboard')],
        ['label' => __('Locais'), 'url' => route($profile . '.locais.index')],
        ['label' => __('Visualizar'), 'url' => route($profile . '.locais.show', $local)],
        ['label' => __('Ocupantes')],
    ])" />

    <x-flash-alerts />

    @php
        $fichaLocalUrl = route($profile . '.locais.ficha-socioeconomica-pdf', $local);
    @endphp

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0 flex-1">
            <x-page-header :eyebrow="__('Imóvel')" :title="config('visitaai_municipio.ocupantes.titulo_listagem')">
                <x-slot name="lead">
                    <p class="text-sm">
                        <span class="font-mono font-semibold text-slate-900 dark:text-slate-100">#{{ $local->loc_codigo_unico }}</span>
                        <span class="text-slate-500 dark:text-slate-400"> · </span>
                        <span class="text-slate-600 dark:text-slate-400">{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}</span>
                    </p>
                </x-slot>
            </x-page-header>
        </div>
        <div class="flex shrink-0 items-center gap-2 self-start">
            @if($profile === 'agente')
                <a href="{{ route($profile . '.locais.moradores.create', $local) }}"
                   class="v-btn-compact v-btn-compact--blue">
                    <x-heroicon-o-plus class="h-4 w-4 shrink-0" aria-hidden="true" />
                    {{ __('Cadastrar ocupante') }}
                </a>
            @endif
        </div>
    </div>

    @php
        $escOpcoes = config('visitaai_municipio.escolaridade_opcoes', []);
        $rendaOpcoes = config('visitaai_municipio.renda_faixa_opcoes', []);
        $corOpcoes = config('visitaai_municipio.cor_raca_opcoes', []);
        $trabOpcoes = config('visitaai_municipio.situacao_trabalho_opcoes', []);
        $local->loadMissing(['moradores', 'visitas']);
        $porMorId = $local->moradores->keyBy('mor_id');
        $visitasComObs = $local->visitas->filter(fn ($v) => is_array($v->vis_ocupantes_observacoes) && count($v->vis_ocupantes_observacoes) > 0);
    @endphp
    <x-section-card class="v-card--flush overflow-hidden dark:bg-gray-800">
        <div class="v-list-toolbar !p-3 sm:!p-4">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <form method="get" class="min-w-0 flex-1">
                    <x-form-field name="ocupantes-q" :label="__('Pesquisar ocupantes')">
                        <div class="flex items-center gap-2">
                        <input id="ocupantes-q" name="q" type="text" value="{{ $search ?? '' }}"
                               data-live-url="{{ route($profile . '.locais.moradores.index', $local) }}"
                               data-live-param="q"
                               data-live-loading-id="search-loading-moradores"
                               class="v-input" placeholder="{{ __('Nome, escolaridade, renda, trabalho...') }}">
                        <span id="search-loading-moradores" class="hidden shrink-0 text-xs text-slate-500 dark:text-slate-400" aria-live="polite">{{ __('Buscando…') }}</span>
                        @if(filled($search ?? ''))
                            <a href="{{ route($profile . '.locais.moradores.index', $local) }}" class="v-btn-compact v-btn-compact--ghost">{{ __('Limpar') }}</a>
                        @endif
                        </div>
                    </x-form-field>
                </form>
            </div>
        </div>

        <div class="v-table-wrap">
            <table class="v-data-table">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Nome / identificação') }}</th>
                        <th scope="col" class="whitespace-nowrap">{{ __('Nascimento') }}</th>
                        <th scope="col">{{ __('Idade') }}</th>
                        <th scope="col">{{ __('Escolaridade') }}</th>
                        <th scope="col">{{ __('Renda') }}</th>
                        <th scope="col">{{ __('Cor/raça') }}</th>
                        <th scope="col">{{ __('Trabalho') }}</th>
                        <th scope="col" class="text-right">{{ __('Ações') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($moradores as $m)
                        <tr>
                            <td class="font-medium text-slate-900 dark:text-slate-100">
                                <div class="inline-flex items-center gap-2">
                                    <span>{{ $m->mor_nome ?: '-' }}</span>
                                    @if($m->mor_referencia_familiar)
                                        <span class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ __('Titular') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="tabular-nums text-slate-700 dark:text-slate-300">{{ $m->mor_data_nascimento ? $m->mor_data_nascimento->format('d/m/Y') : '-' }}</td>
                            <td class="text-slate-700 dark:text-slate-300">{{ $m->idadeAnos() !== null ? $m->idadeAnos() . ' ' . __('anos') : '-' }}</td>
                            <td class="max-w-[11rem] truncate text-slate-700 dark:text-slate-300" title="{{ $m->mor_escolaridade ? ($escOpcoes[$m->mor_escolaridade] ?? $m->mor_escolaridade) : '' }}">{{ $m->mor_escolaridade ? ($escOpcoes[$m->mor_escolaridade] ?? $m->mor_escolaridade) : '-' }}</td>
                            <td class="max-w-[11rem] truncate text-slate-700 dark:text-slate-300" title="{{ $m->mor_renda_faixa ? ($rendaOpcoes[$m->mor_renda_faixa] ?? $m->mor_renda_faixa) : '' }}">{{ $m->mor_renda_faixa ? ($rendaOpcoes[$m->mor_renda_faixa] ?? $m->mor_renda_faixa) : '-' }}</td>
                            <td class="max-w-[9rem] truncate text-slate-700 dark:text-slate-300" title="{{ $m->mor_cor_raca ? ($corOpcoes[$m->mor_cor_raca] ?? $m->mor_cor_raca) : '' }}">{{ $m->mor_cor_raca ? ($corOpcoes[$m->mor_cor_raca] ?? $m->mor_cor_raca) : '-' }}</td>
                            <td class="max-w-[11rem] truncate text-slate-700 dark:text-slate-300" title="{{ $m->mor_situacao_trabalho ? ($trabOpcoes[$m->mor_situacao_trabalho] ?? $m->mor_situacao_trabalho) : '' }}">{{ $m->mor_situacao_trabalho ? ($trabOpcoes[$m->mor_situacao_trabalho] ?? $m->mor_situacao_trabalho) : '-' }}</td>
                            <td class="text-right whitespace-nowrap">
                                <div class="inline-flex justify-end gap-1.5">
                                    <a href="{{ route($profile . '.locais.moradores.ficha-socioeconomica-pdf', [$local, $m]) }}"
                                       class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                       title="{{ __('Baixar ficha socioeconômica') }}"
                                       aria-label="{{ __('Baixar ficha socioeconômica') }}">
                                        <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0" />
                                    </a>
                                    @if($profile === 'agente')
                                        <a href="{{ route($profile . '.locais.moradores.edit', [$local, $m]) }}"
                                           class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-slate-200 bg-white text-slate-600 shadow-sm transition hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-slate-400/40 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-300 dark:hover:bg-slate-700"
                                           title="{{ __('Editar') }}"
                                           aria-label="{{ __('Editar ocupante') }}">
                                            <x-heroicon-o-pencil-square class="h-4 w-4 shrink-0" />
                                        </a>
                                        <form action="{{ route($profile . '.locais.moradores.destroy', [$local, $m]) }}" method="post" class="inline">
                                            @csrf
                                            @method('delete')
                                            <button type="submit"
                                                    data-confirm-message="{{ __('Excluir este registro de ocupante?') }}"
                                                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg border border-red-200 bg-red-50 text-red-600 shadow-sm transition hover:bg-red-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-400/40 dark:border-red-900/50 dark:bg-red-950/40 dark:text-red-300 dark:hover:bg-red-950/60"
                                                    title="{{ __('Excluir') }}"
                                                    aria-label="{{ __('Excluir ocupante') }}">
                                                <x-heroicon-o-trash class="h-4 w-4 shrink-0" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="!p-0">
                                <x-empty-state
                                    :title="__('Nenhum ocupante registrado neste imóvel.')"
                                    icon="heroicon-o-user-group"
                                    class="border-0 bg-transparent px-4 py-10"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($moradores->hasPages())
            <div class="border-t border-gray-200 px-4 py-3 dark:border-gray-600">{{ $moradores->links() }}</div>
        @endif
    </x-section-card>

    @if($visitasComObs->isNotEmpty())
        <x-section-card class="mt-4">
            <h3 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ __('Registros nas visitas sobre ocupantes') }}</h3>
            <ul class="mt-3 space-y-3 text-xs text-slate-800 dark:text-slate-200 sm:text-sm">
                @foreach($visitasComObs as $vis)
                    <li>
                        <x-ui.disclosure variant="muted-card-simple" class="group !rounded-lg !border !border-slate-200/80 !bg-white/70 dark:!border-slate-700/80 dark:!bg-slate-900/40">
                            <x-slot name="summary">
                                <span class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-900 dark:text-slate-100">
                                    <x-heroicon-o-chevron-down class="h-4 w-4 shrink-0 transition-transform duration-200 group-open:rotate-180" aria-hidden="true" />
                                    {{ __('Visita') }} #{{ $vis->vis_id }}, {{ $vis->vis_data ? \Carbon\Carbon::parse($vis->vis_data)->format('d/m/Y') : __('N/D') }}
                                </span>
                            </x-slot>
                            <ul class="space-y-1.5 border-t border-slate-100 pt-2 dark:border-slate-700/80">
                                @foreach($vis->vis_ocupantes_observacoes as $mid => $texto)
                                    @php $midInt = (int) $mid; $nomeMor = optional($porMorId->get($midInt))->mor_nome; @endphp
                                    <li class="text-slate-800 dark:text-slate-200">
                                        <span class="font-mono text-[11px] text-slate-500 dark:text-slate-400">#{{ $midInt }}</span>
                                        @if(filled($nomeMor))
                                            <span class="font-medium">: {{ $nomeMor }}</span>
                                        @endif
                                        <span class="block mt-0.5 whitespace-pre-wrap text-xs">{{ $texto }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </x-ui.disclosure>
                    </li>
                @endforeach
            </ul>
        </x-section-card>
    @endif
</div>

<script>
(function () {
    document.querySelectorAll('[data-confirm-message]').forEach(function (element) {
        element.addEventListener('click', function (event) {
            var message = element.dataset.confirmMessage || '';
            if (!message || confirm(message)) {
                return;
            }
            event.preventDefault();
        });
    });
})();
</script>
@endsection
