@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Relatórios'))
@section('og_description', __('Relatórios de visitas de campo, indicadores do período e resumo do cadastro complementar do imóvel (ocupantes e perfil socioeconômico). Gere PDF conforme os filtros.'))

@section('content')
<!-- Overlay de carregamento -->
<div id="overlayCarregando" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm hidden" role="alertdialog" aria-modal="true" aria-labelledby="overlay-carregando-msg">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white mx-auto mb-4"></div>
        <p id="overlay-carregando-msg" class="text-lg font-semibold text-white">{{ __('Gerando relatório, aguarde…') }}</p>
    </div>
</div>

<div class="v-page">
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Relatórios')]]" />
    <x-page-header :eyebrow="__('Inteligência municipal')" :title="__('Relatórios')">
        <x-slot name="lead">
            <p>{{ __('Gere PDFs e indicadores do período; use os filtros abaixo após aplicar.') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts />

    @if($sem_visitas ?? false)
        <x-section-card>
            <div class="v-empty-state py-12">
                <div class="v-empty-state__icon h-20 w-20" aria-hidden="true">
                    <x-heroicon-o-document-text class="h-10 w-10 shrink-0" />
                </div>
                <p class="v-empty-state__title text-lg">{{ __('Nenhuma visita cadastrada') }}</p>
                <p class="v-empty-state__text max-w-md">
                    {{ __('Não há visitas no sistema. Relatórios, indicadores e PDF ficam disponíveis após o cadastro de visitas pelos profissionais de campo.') }}
                </p>
                <p class="mt-2 text-xs text-slate-500 dark:text-slate-500">{{ __('Inclui perfis ACE e ACS.') }}</p>
                <a href="{{ route('gestor.visitas.index') }}" class="v-btn-compact v-btn-compact--blue mt-6">
                    <x-heroicon-o-eye class="h-4 w-4 shrink-0" aria-hidden="true" />
                    {{ __('Ir para visitas') }}
                </a>
            </div>
        </x-section-card>
    @else
    <div
        x-data="{
            tipo: '{{ request('tipo_relatorio', 'completo') }}',
            filtrosAplicados: false,
            filtrosAlterados: false,
            appliedParams: { data_unica: '', data_inicio: '', data_fim: '', local_ids: [] },
            relPdfMsgs: @js([
                'filtrosAlterados' => __('Você alterou os filtros. Clique em Filtrar antes de gerar o PDF.'),
                'diario' => __('Selecione a data para o relatório diário e clique em Filtrar.'),
                'semanal' => __('Selecione as datas de início e fim e clique em Filtrar.'),
                'individual' => __('Selecione ao menos um local e clique em Filtrar.'),
                'generico' => __('Aplique os filtros antes de gerar o PDF.'),
            ]),
            get botaoAtivo() {
                if (this.filtrosAlterados) return false;
                if (this.tipo === 'completo') return true;
                if (this.tipo === 'diario') return (this.appliedParams.data_unica || '') !== '';
                if (this.tipo === 'semanal') return (this.appliedParams.data_inicio || '') !== '' && (this.appliedParams.data_fim || '') !== '';
                if (this.tipo === 'individual') return Array.isArray(this.appliedParams.local_ids) && this.appliedParams.local_ids.length > 0;
                return false;
            }
        }"
        x-init="
            var p = new URLSearchParams(window.location.search);
            appliedParams = {
                data_unica: p.get('data_unica') || '',
                data_inicio: p.get('data_inicio') || '',
                data_fim: p.get('data_fim') || '',
                local_ids: p.getAll('local_id[]') || []
            };
            filtrosAplicados = p.toString() !== '';
            $watch('tipo', function() { filtrosAplicados = false; filtrosAlterados = true; });
        "
        @@filtro-alterado.window="filtrosAlterados = true"
        class="space-y-4">

        <div class="flex flex-col items-end gap-2 sm:flex-row sm:items-center sm:justify-end">
            <div class="flex w-full max-w-xl flex-col items-end gap-2 text-right sm:w-auto sm:max-w-md">
                <p x-show="filtrosAlterados" x-cloak class="text-sm font-medium text-amber-600 dark:text-amber-400">
                    {{ __('Você alterou os filtros. Clique em') }} <strong>{{ __('Filtrar') }}</strong> {{ __('antes de gerar o PDF para que o relatório use os dados corretos.') }}
                </p>
                <button type="button" :disabled="!botaoAtivo"
                    @@click.prevent="if (!botaoAtivo) {
                        let msg = filtrosAlterados ? relPdfMsgs.filtrosAlterados : '';
                        if (!msg && tipo === 'diario' && !appliedParams.data_unica) msg = relPdfMsgs.diario;
                        if (!msg && tipo === 'semanal' && (!appliedParams.data_inicio || !appliedParams.data_fim)) msg = relPdfMsgs.semanal;
                        if (!msg && tipo === 'individual' && (!appliedParams.local_ids || appliedParams.local_ids.length === 0)) msg = relPdfMsgs.individual;
                        if (!msg) msg = relPdfMsgs.generico;
                        alert(msg);
                        return;
                    } gerarBase64Graficos();"
                    class="v-btn-export v-btn-export--pdf">
                    <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0" aria-hidden="true" />
                    {{ __('Gerar relatório em PDF') }}
                </button>
            </div>
        </div>

        <x-section-card>
            <h2 class="v-section-title mb-1">{{ __('Filtros') }}</h2>
            <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">{{ __('Para o filtro funcionar, todos os campos marcados com * devem ser preenchidos ou selecionados.') }}</p>
            <form method="GET" x-ref="formulario" @@submit.prevent="filtrosAplicados = true; $nextTick(() => $refs.formulario.submit())"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
                <div>
                    <label class="v-toolbar-label mb-1">{{ __('Tipo') }} <span class="text-red-500">*</span></label>
                    <select name="tipo_relatorio" x-model="tipo" class="v-select">
                        <option value="completo" {{ request('tipo_relatorio', 'completo') === 'completo' ? 'selected' : '' }}>{{ __('Completo') }}</option>
                        <option value="diario">{{ __('Diário') }}</option>
                        <option value="semanal">{{ __('Por período') }}</option>
                        <option value="individual">{{ __('Individual') }}</option>
                    </select>
                </div>
                <div x-show="tipo === 'diario'" x-cloak>
                    <label class="v-toolbar-label mb-1">{{ __('Data') }} <span class="text-red-500">*</span></label>
                    <input type="date" name="data_unica" value="{{ request('data_unica') }}" @@change="filtrosAlterados = true" class="v-input" />
                </div>
                <template x-if="tipo === 'semanal'">
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <label class="v-toolbar-label mb-1">{{ __('Início') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" @@change="filtrosAlterados = true" class="v-input" />
                        </div>
                        <div>
                            <label class="v-toolbar-label mb-1">{{ __('Fim') }} <span class="text-red-500">*</span></label>
                            <input type="date" name="data_fim" value="{{ request('data_fim') }}" @@change="filtrosAlterados = true" class="v-input" />
                        </div>
                    </div>
                </template>
                <div x-show="tipo === 'individual'" x-cloak class="md:col-span-3"
                    x-data="{
                        open: false,
                        search: '',
                        options: [],
                        selected: [],
                        get filtered() {
                            const q = (this.search || '').toLowerCase();
                            return this.options.filter(o => (o.label || '').toLowerCase().includes(q));
                        },
                        toggle(opt) {
                            if (this.selected.some(s => s.id === opt.id)) {
                                this.selected = this.selected.filter(s => s.id !== opt.id);
                            } else {
                                this.selected = [...this.selected, opt];
                            }
                            this.$dispatch('filtro-alterado');
                        },
                        isSelected(id) { return this.selected.some(s => s.id === id); }
                    }"
                    x-init="
                        (function(){
                            var decode = function(s) {
                                if (!s || !s.includes('&')) return s || '[]';
                                var d = document.createElement('div');
                                d.innerHTML = s;
                                return d.textContent || d.innerText || s;
                            };
                            options = JSON.parse(decode($el.getAttribute('data-local-options')) || '[]');
                            var sel = JSON.parse(decode($el.getAttribute('data-local-selected')) || '[]');
                            selected = Array.isArray(sel) ? sel : [];
                        })();
                    "
                    data-local-options="{{ e(json_encode($locaisParaSelectArray ?? [])) }}"
                    data-local-selected="{{ e(json_encode(
                        array_values(array_map(function($id) use ($locaisParaSelectArray) {
                            $id = (int) $id;
                            $arr = $locaisParaSelectArray ?? [];
                            $item = collect($arr)->firstWhere('id', $id);
                            return ['id' => $id, 'label' => $item ? ($item['label'] ?? 'Local #'.$id) : 'Local #'.$id];
                        }, (array) request('local_id', [])))
                    )) }}"
                    @@click.outside="open = false">
                    <label class="v-toolbar-label mb-1">{{ __('Local(is)') }} <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div @@click="open = !open" class="v-input flex min-h-[2.5rem] cursor-pointer flex-wrap items-center gap-1.5">
                            <template x-for="item in selected" :key="item.id">
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-sm text-slate-800 dark:bg-slate-800/90 dark:text-slate-200">
                                    <span x-text="item.label" class="truncate max-w-[200px]"></span>
                                    <button type="button" @@click.stop="selected = selected.filter(s => s.id !== item.id); $dispatch('filtro-alterado')" class="leading-none hover:text-slate-950 dark:hover:text-white" aria-label="{{ __('Remover') }}">×</button>
                                </span>
                            </template>
                            <input x-show="open" x-model="search" @@click.stop type="text" placeholder="{{ __('Buscar local...') }}" class="flex-1 min-w-[120px] bg-transparent border-0 p-0 text-sm placeholder-gray-500 focus:ring-0 focus:outline-none"
                                   @@keydown.escape="open = false">
                            <span x-show="!open && selected.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Selecione um Local') }}</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-20 w-full mt-1 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 max-h-52 overflow-y-auto">
                            <template x-for="opt in filtered" :key="opt.id">
                                <div @@click="toggle(opt)" role="option"
                                     class="v-list-item-hover flex w-full cursor-pointer items-center justify-between px-3 py-2 text-sm"
                                     :class="{ 'bg-slate-100 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100': isSelected(opt.id) }">
                                    <span x-text="opt.label" class="truncate flex-1"></span>
                                    <span x-show="isSelected(opt.id)" class="ml-2 text-slate-600 dark:text-slate-300">✓</span>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">{{ __('Nenhum local encontrado') }}</div>
                        </div>
                    </div>
                    <template x-for="item in selected" :key="item.id">
                        <input type="hidden" :name="'local_id[]'" :value="item.id">
                    </template>
                </div>
                <div x-show="tipo !== 'individual'" x-cloak :class="tipo === 'completo' ? 'md:col-span-3' : ''"
                    x-data="{
                        open: false,
                        search: '',
                        options: [],
                        selected: [],
                        emptyBairroCadastrado: @js(__('Nenhum bairro cadastrado nos locais.')),
                        emptyBairroBusca: @js(__('Nenhum bairro encontrado para a busca.')),
                        get filtered() {
                            const q = this.search.toLowerCase();
                            return this.options.filter(o => o.toLowerCase().includes(q));
                        },
                        toggle(opt) {
                            if (this.selected.includes(opt)) {
                                this.selected = this.selected.filter(s => s !== opt);
                            } else {
                                this.selected = [...this.selected, opt];
                            }
                            this.$dispatch('filtro-alterado');
                        }
                    }"
                    x-init="
                        (function(){
                            var decode = function(s) {
                                if (!s || !s.includes('&')) return s || '[]';
                                var d = document.createElement('div');
                                d.innerHTML = s;
                                return d.textContent || d.innerText || s;
                            };
                            var rawOpts = $el.getAttribute('data-options');
                            var rawSel = $el.getAttribute('data-selected');
                            options = JSON.parse(decode(rawOpts) || '[]');
                            selected = JSON.parse(decode(rawSel) || '[]');
                        })();
                    "
                    data-options="{{ e(json_encode($bairros ?? [])) }}"
                    data-selected="{{ e(json_encode(array_values((array) request('bairro', [])))) }}"
                    @@click.outside="open = false">
                    <label class="v-toolbar-label mb-1">{{ __('Bairro(s)') }}</label>
                    <div class="relative">
                        <div @@click="open = !open" class="v-input flex min-h-[2.5rem] cursor-pointer flex-wrap items-center gap-1.5 focus-within:ring-2 focus-within:ring-blue-500/30 focus-within:ring-offset-0">
                            <template x-for="val in selected" :key="val">
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-sm text-slate-800 dark:bg-slate-800/90 dark:text-slate-200">
                                    <span x-text="val"></span>
                                    <button type="button" @@click.stop="selected = selected.filter(s => s !== val); $dispatch('filtro-alterado')" class="leading-none hover:text-slate-950 dark:hover:text-white" aria-label="{{ __('Remover') }}">×</button>
                                </span>
                            </template>
                            <input x-show="open" x-model="search" @@click.stop type="text" placeholder="{{ __('Buscar bairro...') }}" class="flex-1 min-w-[120px] bg-transparent border-0 p-0 text-sm placeholder-gray-500 focus:ring-0 focus:outline-none"
                                   @@keydown.escape="open = false">
                            <span x-show="!open && selected.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Selecione um ou mais bairros...') }}</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-20 w-full mt-1 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 max-h-52 overflow-y-auto">
                            <template x-for="opt in filtered" :key="opt">
                                <div @@click="toggle(opt)" role="option" :aria-selected="selected.includes(opt)"
                                     class="v-list-item-hover flex cursor-pointer items-center justify-between px-3 py-2 text-sm"
                                     :class="{ 'bg-slate-100 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100': selected.includes(opt) }">
                                    <span x-text="opt"></span>
                                    <span x-show="selected.includes(opt)" class="text-slate-600 dark:text-slate-300">✓</span>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-text="options.length === 0 ? emptyBairroCadastrado : emptyBairroBusca"></div>
                        </div>
                    </div>
                    <template x-for="val in selected" :key="val">
                        <input type="hidden" :name="'bairro[]'" :value="val">
                    </template>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 justify-end items-stretch sm:items-center">
                    <button type="submit" class="v-btn-primary h-10 px-5">{{ __('Filtrar') }}</button>
                    <a href="{{ route('gestor.relatorios.index') }}" class="v-btn-secondary h-10 px-5">{{ __('Limpar') }}</a>
                </div>
            </form>
        </x-section-card>
    </div>

    {{-- Indicadores (estilo dashboard) --}}
    <section class="space-y-4">
        <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <h2 class="v-section-title text-base">{{ __('Indicadores do período') }}</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Valores conforme filtros aplicados.') }}</p>
        </div>
        <div class="grid grid-cols-1 items-stretch gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-3">
            <x-ui.stat-tile :heading="__('Total de visitas')">
                <x-slot name="icon">
                    <x-heroicon-o-clipboard-document-list class="h-5 w-5 shrink-0 text-blue-600 dark:text-blue-400" />
                </x-slot>
                {{ $totalVisitas }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Visitas com pendência')">
                <x-slot name="icon">
                    <x-heroicon-o-clock class="h-5 w-5 shrink-0 text-amber-500 dark:text-amber-400" />
                </x-slot>
                {{ $percentualPendencias }}% <span class="text-sm font-normal text-slate-500 dark:text-slate-400">({{ $totalComPendencia }})</span>
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Depósitos eliminados')">
                <x-slot name="icon">
                    <x-heroicon-o-building-office-2 class="h-5 w-5 shrink-0 text-blue-500 dark:text-blue-400" />
                </x-slot>
                {{ $totalDepEliminados }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Visitas com tratamento')">
                <x-slot name="icon">
                    <x-heroicon-o-check-circle class="h-5 w-5 shrink-0 text-blue-500 dark:text-blue-400" />
                </x-slot>
                {{ $visitasComTratamento }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Coletas realizadas')">
                <x-slot name="icon">
                    <x-heroicon-o-beaker class="h-5 w-5 shrink-0 text-blue-500 dark:text-blue-400" />
                </x-slot>
                {{ $totalComColeta }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Bairro com mais ocorrências')" valueClass="mt-2 text-lg font-semibold text-slate-900 dark:text-slate-100">
                <x-slot name="icon">
                    <x-heroicon-o-map-pin class="h-5 w-5 shrink-0 text-slate-500 dark:text-slate-400" />
                </x-slot>
                {{ $bairroMaisFrequente ?: __('N/D') }}
            </x-ui.stat-tile>
        </div>
        <div class="mt-6 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
            <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Cadastro complementar do imóvel') }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Imóveis vinculados às visitas do período filtrado.') }}</p>
        </div>
        <div class="mt-3 grid grid-cols-1 items-stretch gap-3 sm:grid-cols-2 sm:gap-4 lg:grid-cols-4">
            <x-ui.stat-tile :heading="__('Imóveis distintos no período')">
                <x-slot name="icon">
                    <x-heroicon-o-home class="h-5 w-5 shrink-0 text-slate-600 dark:text-slate-400" />
                </x-slot>
                {{ $statsComplemento['imoveis_periodo'] ?? 0 }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Imóveis com ocupantes cadastrados')">
                <x-slot name="icon">
                    <x-heroicon-o-user-group class="h-5 w-5 shrink-0 text-slate-600 dark:text-slate-400" />
                </x-slot>
                {{ $statsComplemento['imoveis_com_ocupantes'] ?? 0 }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Total de ocupantes (cadastro)')">
                <x-slot name="icon">
                    <x-heroicon-o-users class="h-5 w-5 shrink-0 text-slate-600 dark:text-slate-400" />
                </x-slot>
                {{ $statsComplemento['total_ocupantes'] ?? 0 }}
            </x-ui.stat-tile>
            <x-ui.stat-tile :heading="__('Imóveis com ficha socioeconômica')">
                <x-slot name="icon">
                    <x-heroicon-o-clipboard-document-check class="h-5 w-5 shrink-0 text-slate-600 dark:text-slate-400" />
                </x-slot>
                {{ $statsComplemento['imoveis_com_socioeconomico'] ?? 0 }}
            </x-ui.stat-tile>
        </div>
    </section>

    @if(isset($imoveisComplementoResumo) && $imoveisComplementoResumo->isNotEmpty())
    <x-section-card>
        <h2 class="v-section-title mb-1 text-base">{{ __('Imóveis no período: cadastro complementar') }}</h2>
        <p class="mb-4 text-sm text-slate-600 dark:text-slate-400">{{ __('Resumo por imóvel: quantidade de ocupantes e indicação de ficha socioeconômica. O PDF inclui a mesma tabela.') }}</p>
        <div class="v-table-wrap overflow-x-auto">
            <table class="v-data-table min-w-[42rem]">
                <thead>
                    <tr>
                        <th scope="col">{{ __('Cód.') }}</th>
                        <th scope="col" class="min-w-[10rem]">{{ __('Endereço') }}</th>
                        <th scope="col">{{ __('Bairro') }}</th>
                        <th scope="col" class="text-center">{{ __('Ocupantes') }}</th>
                        <th scope="col" class="text-center">{{ __('Socioecon.') }}</th>
                        <th scope="col" class="text-center">{{ __('Moradores (declarado)') }}</th>
                        <th scope="col" class="min-w-[8rem]">{{ __('Faixa renda familiar') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($imoveisComplementoResumo as $loc)
                        @php
                            $lse = $loc->socioeconomico;
                        @endphp
                        <tr>
                            <td class="whitespace-nowrap font-mono text-xs">{{ $loc->loc_codigo_unico ?? '-' }}</td>
                            <td>{{ trim(($loc->loc_endereco ?? '').($loc->loc_numero ? ', '.$loc->loc_numero : '')) ?: '-' }}</td>
                            <td>{{ $loc->loc_bairro ?? '-' }}</td>
                            <td class="text-center tabular-nums">{{ (int) ($loc->moradores_count ?? 0) }}</td>
                            <td class="text-center">{{ $lse ? __('Sim') : __('Não') }}</td>
                            <td class="text-center tabular-nums">{{ $lse && $lse->lse_n_moradores_declarado !== null ? $lse->lse_n_moradores_declarado : '-' }}</td>
                            <td class="text-sm text-slate-600 dark:text-slate-400">{{ $lse && $lse->lse_renda_familiar_faixa ? \Illuminate\Support\Str::limit($lse->lse_renda_familiar_faixa, 48) : '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-section-card>
    @endif

    {{-- Gráficos: apenas os 4 mais relevantes para o relatório --}}
    <x-section-card>
        <h2 class="v-section-title mb-4 text-base">{{ __('Análise visual') }}</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:grid-rows-2 sm:auto-rows-fr">
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">{{ __('Visitas por bairro') }}</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoBairros" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoBairrosVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">{{ __('Sem dados no período.') }}</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">{{ __('Doenças registradas') }}</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoDoencas" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoDoencasVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">{{ __('Nenhuma doença registrada.') }}</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">{{ __('Visitas por dia') }}</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoDias" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoDiasVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">{{ __('Sem dados no período.') }}</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">{{ __('Formas de tratamento') }}</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoTratamentos" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoTratamentosVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">{{ __('Nenhum tratamento registrado.') }}</p>
                </div>
            </div>
        </div>
    </x-section-card>

    {{-- Mapa de calor --}}
    <x-section-card>
        <h2 class="v-section-title mb-1">{{ __('Mapa de calor') }}</h2>
        <p class="mb-3 text-sm text-slate-500 dark:text-slate-400">{{ __('Distribuição das visitas no mapa do município.') }}</p>
        <div class="relative h-64 w-full overflow-hidden rounded-lg border" style="border-color: rgb(var(--v-border) / 1);">
            <div id="mapa-calor" class="w-full h-full rounded-lg"></div>
            <p id="mapa-calor-vazio" class="hidden absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/80 rounded-lg m-0">{{ __('Nenhuma visita com localização no período.') }}</p>
        </div>
    </x-section-card>

    {{-- Tabela de visitas --}}
    <x-section-card>
    <h2 class="v-section-title mb-4">{{ __('Visitas registradas') }}</h2>

    <div class="v-table-wrap">
        <table class="v-data-table">
            <thead>
                <tr>
                    <th scope="col" class="whitespace-nowrap">{{ __('Código') }}</th>
                    <th scope="col">{{ __('Data') }}</th>
                    <th scope="col">{{ __('Pendência') }}</th>
                    <th scope="col" class="min-w-[12rem]">{{ __('Local') }}</th>
                    <th scope="col">{{ __('Atividade') }}</th>
                    <th scope="col" class="align-bottom">
                        <span class="block leading-tight normal-case tracking-normal">{{ __('Profissional') }}</span>
                        <span class="mt-0.5 block text-[10px] font-normal normal-case tracking-normal text-slate-500 dark:text-slate-400">{{ __('ACE ou ACS') }}</span>
                    </th>
                    <th scope="col" class="w-14 text-center">{{ __('Ações') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($visitasPaginated as $visita)
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
                                    $revisitaPosterior = $visita->local
                                        ? $visita->local->visitas()
                                            ->where('vis_data', '>', $visita->vis_data)
                                            ->orderBy('vis_data')
                                            ->first()
                                        : null;
                                @endphp
                                @if($revisitaPosterior)
                                    <div class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Revisitado em :data', ['data' => \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y')]) }}</div>
                                @endif
                            @else
                                <span class="inline-flex rounded-md bg-emerald-100 px-2 py-0.5 text-xs font-semibold text-emerald-900 dark:bg-emerald-950/45 dark:text-emerald-200">{{ __('Concluída') }}</span>
                            @endif
                        </td>
                        <td class="leading-snug">
                            @if($visita->local)
                                <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                                <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                                    {{ __('Bairro/Localidade: :bairro · Cód.: :codigo', ['bairro' => $visita->local->loc_bairro, 'codigo' => $visita->local->loc_codigo_unico]) }}
                                </div>
                                @if(($visita->local->moradores_count ?? 0) > 0)
                                    <div class="mt-1 text-xs font-medium text-slate-600 dark:text-slate-300">{{ __(':n ocupante(s) no cadastro do imóvel', ['n' => $visita->local->moradores_count]) }}</div>
                                @endif
                                @if($visita->local->socioeconomico)
                                    <div class="mt-0.5 text-xs text-emerald-700 dark:text-emerald-300">{{ __('Ficha socioeconômica do imóvel preenchida') }}</div>
                                @endif
                            @else
                                <p class="text-xs font-medium text-amber-700 dark:text-amber-300">{{ __('Local não disponível para esta visita.') }}</p>
                            @endif
                        </td>
                        <td class="text-slate-600 dark:text-slate-300" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                            {{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: __('Não informado') }}
                        </td>
                        <td class="whitespace-nowrap text-slate-900 dark:text-slate-100">
                            {{ $visita->usuario->use_nome ?? '-' }}
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
                            <div class="px-4 py-8 text-center text-sm italic text-slate-600 dark:text-slate-400">{{ __('Nenhuma visita encontrada.') }}</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($visitasPaginated))
        <x-pagination-relatorio :paginator="$visitasPaginated" item-label="visitas" />
    @endif
    @if(isset($visitasPaginated) && $visitasPaginated->total() == 0)
        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">{{ __('Nenhuma visita no período.') }}</p>
    @endif
    </x-section-card>

</div>
    @endif

{{-- Scripts (apenas quando há visitas; estado vazio não carrega gráficos/mapa) --}}
@if(!($sem_visitas ?? false))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat@0.2.0/dist/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet-image/leaflet-image.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

@php
    $relatoriosI18nCharts = [
        'visitas' => __('Visitas'),
        'desconhecido' => __('Desconhecido'),
        'ni' => __('N/I'),
        'indefinida' => __('Indefinida'),
    ];
@endphp
<script>
document.addEventListener('DOMContentLoaded', function() {
    const REL_I18N = @json($relatoriosI18nCharts);
    const visitas = @json($visitasParaGraficos ?? []);

    const contagemPorBairro = {};
    const contagemPorDoenca = {};

    (Array.isArray(visitas) ? visitas : []).forEach(v => {
        const bairro = (v.local && v.local.loc_bairro) ? String(v.local.loc_bairro) : REL_I18N.desconhecido;
        contagemPorBairro[bairro] = (contagemPorBairro[bairro] || 0) + 1;
        (v.doencas || []).forEach(d => {
            const nome = (d && d.doe_nome) ? String(d.doe_nome) : REL_I18N.ni;
            contagemPorDoenca[nome] = (contagemPorDoenca[nome] || 0) + 1;
        });
    });

    const labelsBairro = Object.keys(contagemPorBairro);
    const dataBairro = Object.values(contagemPorBairro);
    const elBairros = document.getElementById('graficoBairros');
    const elBairrosVazio = document.getElementById('graficoBairrosVazio');
    if (labelsBairro.length > 0 && elBairros) {
        new Chart(elBairros, {
            type: 'bar',
            data: {
                labels: labelsBairro,
                datasets: [{ label: REL_I18N.visitas, data: dataBairro, backgroundColor: 'rgba(59, 130, 246, 0.6)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false }, title: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    } else {
        if (elBairros) elBairros.classList.add('hidden');
        if (elBairrosVazio) elBairrosVazio.classList.remove('hidden');
    }

    const labelsDoenca = Object.keys(contagemPorDoenca);
    const dataDoenca = Object.values(contagemPorDoenca);
    const elDoencas = document.getElementById('graficoDoencas');
    const elDoencasVazio = document.getElementById('graficoDoencasVazio');
    if (labelsDoenca.length > 0 && elDoencas) {
        const bluePiePalette = ['#172554', '#1e3a8a', '#1e40af', '#1d4ed8', '#2563eb', '#3b82f6', '#60a5fa', '#1d4ed8'];
        const bgDoenca = labelsDoenca.map((_, i) => bluePiePalette[i % bluePiePalette.length]);
        new Chart(elDoencas, {
            type: 'pie',
            data: {
                labels: labelsDoenca,
                datasets: [{ data: dataDoenca, backgroundColor: bgDoenca }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: { display: false },
                    legend: { position: 'bottom', labels: { color: '#9ca3af' } },
                    datalabels: { formatter: (val, ctx) => { const t = ctx.chart.data.datasets[0].data.reduce((a,b)=>a+b,0); return t ? ((val/t)*100).toFixed(0)+'%' : ''; }, color: '#fff', font: { weight: 'bold' } }
                }
            },
            plugins: [ChartDataLabels]
        });
    } else {
        if (elDoencas) elDoencas.classList.add('hidden');
        if (elDoencasVazio) elDoencasVazio.classList.remove('hidden');
    }

    // Mapa de calor: só pontos com lat/lng numéricos válidos (NaN não pode passar: quebrava o setView)
    const parsePontoCalor = (v) => {
        const loc = v?.local;
        if (!loc) return null;
        const rawLat = loc.loc_latitude;
        const rawLng = loc.loc_longitude;
        if (rawLat == null || rawLng == null) return null;
        if (String(rawLat).trim() === '' || String(rawLng).trim() === '') return null;
        const lat = parseFloat(String(rawLat).trim().replace(',', '.'));
        const lng = parseFloat(String(rawLng).trim().replace(',', '.'));
        if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
        if (Math.abs(lat) > 90 || Math.abs(lng) > 180) return null;
        if (lat === 0 && lng === 0) return null;
        return [lat, lng, 1];
    };
    const pontos = (Array.isArray(visitas) ? visitas : [])
        .map(parsePontoCalor)
        .filter(p => p !== null);

    let centroLat = -28.65, centroLng = -52.42;
    if (pontos.length > 0) {
        const total = pontos.length;
        centroLat = pontos.reduce((sum, p) => sum + p[0], 0) / total;
        centroLng = pontos.reduce((sum, p) => sum + p[1], 0) / total;
    }

    const elMapaCalor = document.getElementById('mapa-calor');
    let mapaCalor = null;
    const mapa = elMapaCalor ? L.map('mapa-calor').setView([centroLat, centroLng], 14) : null;

    if (mapa) {
        mapaCalor = mapa;
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors',
            maxZoom: 18,
        }).addTo(mapa);

        if (pontos.length > 0 && typeof L.heatLayer === 'function') {
            L.heatLayer(pontos, {
                radius: 28,
                blur: 18,
                maxZoom: 17,
                minOpacity: 0.35,
                gradient: { 0.1: '#bfdbfe', 0.35: '#60a5fa', 0.55: '#2563eb', 0.8: '#1d4ed8', 1.0: '#172554' }
            }).addTo(mapa);
            try {
                const bounds = L.latLngBounds(pontos.map(p => [p[0], p[1]]));
                if (bounds.isValid()) {
                    mapa.fitBounds(bounds, { padding: [40, 40], maxZoom: 16 });
                }
            } catch (e) { /* ignore */ }
        } else if (pontos.length > 0) {
            console.warn('Leaflet.heat: L.heatLayer indisponível (script não carregou).');
        }

        requestAnimationFrame(function () {
            mapa.invalidateSize();
            setTimeout(function () { mapa.invalidateSize(); }, 200);
        });
    }

    if (pontos.length === 0) {
        const vazio = document.getElementById('mapa-calor-vazio');
        if (vazio) vazio.classList.remove('hidden');
    }

    // Função para gerar o PDF (exposta globalmente para o botão)
    window.gerarBase64Graficos = async function gerarBase64Graficos() {
        // Mostrar overlay
        document.getElementById('overlayCarregando').classList.remove('hidden');

        await new Promise(resolve => setTimeout(resolve, 300));

        const canvasBairros = document.getElementById('graficoBairros');
        const canvasDoencas = document.getElementById('graficoDoencas');
        const canvasDias = document.getElementById('graficoDias');
        const canvasTratamentos = document.getElementById('graficoTratamentos');

        const base64Bairros = canvasBairros && !canvasBairros.classList.contains('hidden') ? canvasBairros.toDataURL('image/png') : '';
        const base64Doencas = canvasDoencas && !canvasDoencas.classList.contains('hidden') ? canvasDoencas.toDataURL('image/png') : '';
        const base64Mapa = await new Promise(resolve => {
            if (!mapaCalor || typeof leafletImage !== 'function') {
                return resolve('');
            }
            leafletImage(mapaCalor, function(err, canvas) {
                if (err || !canvas) {
                    console.error(@json(__('Erro ao capturar o mapa:')), err);
                    return resolve("");
                }
                resolve(canvas.toDataURL("image/png"));
            });
        });

        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("gestor.relatorios.pdf") }}';
        form.target = '_blank';

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '{{ csrf_token() }}';
        form.appendChild(token);

        const addField = (name, value) => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            input.value = value;
            form.appendChild(input);
        }

        // Usar filtros aplicados na página (URL), não o estado atual do formulário
        const params = new URLSearchParams(window.location.search);
        const tipoRelatorio = params.get('tipo_relatorio') || 'completo';
        const dataUnica = params.get('data_unica') || '';
        const dataInicio = params.get('data_inicio') || '';
        const dataFim = params.get('data_fim') || '';
        const bairros = params.getAll('bairro[]').filter(Boolean);
        const localIds = params.getAll('local_id[]').filter(Boolean);

        addField('data_unica', dataUnica);
        addField('tipo_relatorio', tipoRelatorio);
        addField('data_inicio', dataInicio);
        addField('data_fim', dataFim);
        bairros.forEach(function(b) { addField('bairro[]', b); });
        if (tipoRelatorio === 'individual') {
            localIds.forEach(function(id) { addField('local_id[]', id); });
        }

        // Imagens em base64
        addField('graficoBairrosBase64', base64Bairros);
        addField('graficoDoencasBase64', base64Doencas);
        addField('mapaCalorBase64', base64Mapa);
        addField('graficoZonasBase64', '');
        addField('graficoDiasBase64', canvasDias && !canvasDias.classList.contains('hidden') ? canvasDias.toDataURL('image/png') : '');
        addField('graficoInspBase64', '');
        addField('graficoTratamentosBase64', canvasTratamentos && !canvasTratamentos.classList.contains('hidden') ? canvasTratamentos.toDataURL('image/png') : '');

        document.body.appendChild(form);
        form.submit();

        // Recarrega a página após o envio
        setTimeout(() => {
            document.getElementById('overlayCarregando').classList.add('hidden');
            location.reload();
        }, 1000);
    }

    // Gráfico de evolução diária
    const contagemPorData = {};
    (Array.isArray(visitas) ? visitas : []).forEach(v => { contagemPorData[v.vis_data] = (contagemPorData[v.vis_data] || 0) + 1; });
    const datasOrdenadas = Object.keys(contagemPorData).sort();
    const elDias = document.getElementById('graficoDias');
    const elDiasVazio = document.getElementById('graficoDiasVazio');
    if (datasOrdenadas.length > 0 && elDias) {
        new Chart(elDias, {
            type: 'line',
            data: {
                labels: datasOrdenadas,
                datasets: [{ label: REL_I18N.visitas, data: datasOrdenadas.map(d => contagemPorData[d]), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.2)', tension: 0.3, fill: true }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false }, title: { display: false } },
                scales: { x: { ticks: { color: '#9ca3af', maxTicksLimit: 8 } }, y: { beginAtZero: true, ticks: { color: '#9ca3af' } } }
            }
        });
    } else {
        if (elDias) elDias.classList.add('hidden');
        if (elDiasVazio) elDiasVazio.classList.remove('hidden');
    }

    // Gráfico de formas de tratamento
    const contagemTratamentoForma = {};
    (Array.isArray(visitas) ? visitas : []).forEach(v => {
        (v.tratamentos || []).forEach(t => {
            const forma = t.trat_forma ?? REL_I18N.indefinida;
            contagemTratamentoForma[forma] = (contagemTratamentoForma[forma] || 0) + 1;
        });
    });
    const labelsTrat = Object.keys(contagemTratamentoForma);
    const dataTrat = Object.values(contagemTratamentoForma);
    const elTrat = document.getElementById('graficoTratamentos');
    const elTratVazio = document.getElementById('graficoTratamentosVazio');
    if (labelsTrat.length > 0 && elTrat) {
        new Chart(elTrat, {
            type: 'pie',
            data: {
                labels: labelsTrat,
                datasets: [{ data: dataTrat, backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa', '#93c5fd', '#1d4ed8'] }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { title: { display: false }, legend: { position: 'bottom', labels: { color: '#9ca3af' } } }
            }
        });
    } else {
        if (elTrat) elTrat.classList.add('hidden');
        if (elTratVazio) elTratVazio.classList.remove('hidden');
    }

}); // DOMContentLoaded
</script>
@endif
@endsection