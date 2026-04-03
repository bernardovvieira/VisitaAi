@extends('layouts.app')

@section('og_title', config('app.name') . ' · Relatórios')
@section('og_description', 'Relatórios e indicadores epidemiológicos. Gere PDF e visualize indicadores do período selecionado.')

@section('content')
<!-- Overlay de carregamento -->
<div id="overlayCarregando" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-white text-lg font-semibold">Gerando relatório, aguarde...</p>
    </div>
</div>

<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Relatórios</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded relative mb-4">
            <strong>Erro:</strong> {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="relative mb-4 rounded-lg border border-slate-300 bg-slate-100 px-4 py-3 text-slate-900 dark:border-slate-600 dark:bg-slate-800/70 dark:text-slate-100">
            {{ session('info') }}
        </div>
    @endif

    @if($sem_visitas ?? false)
        {{-- Estado vazio: nenhuma visita no sistema · sem ações disponíveis --}}
        <div class="rounded-xl border border-gray-200/80 bg-white dark:bg-gray-800 shadow-sm px-8 py-12 text-center">
            <div class="max-w-md mx-auto pt-12 pb-12">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <x-heroicon-o-document-text class="h-10 w-10 shrink-0 text-gray-400 dark:text-gray-500" />
                </div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Nenhuma visita cadastrada</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    Não há visitas no sistema. Os relatórios, indicadores e a geração de PDF ficarão disponíveis após o cadastro de visitas pelos profissionais de campo.
                </p>
                <p class="text-xs text-gray-500/90 dark:text-gray-500 mb-6">Inclui perfis ACE e ACS.</p>
                <a href="{{ route('gestor.visitas.index') }}" class="btn-acesso-principal inline-flex items-center px-4 py-2 text-white font-medium rounded-lg transition">
                    <x-heroicon-o-eye class="mr-2 h-5 w-5 shrink-0" />
                    Ir para Visitas
                </a>
            </div>
        </div>
    @else
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Relatórios e indicadores</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Gere relatórios em PDF e visualize indicadores do período selecionado. Use os filtros abaixo.
        </p>
        <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">
            Atividades e terminologia: ACE, ACS, tipos de visita e códigos, alinhadas às recomendações do Ministério da Saúde para vigilância entomológica e controle vetorial.
        </p>
        <p class="mt-1.5 text-[10px] leading-relaxed text-gray-400/95 dark:text-gray-500">
            Referências: Lei 11.350/2006; Diretrizes Nacionais para Prevenção e Controle das Arboviroses Urbanas.
        </p>
    </section>
    {{-- Filtros e PDF --}}
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filtros e relatório</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            <span class="text-red-500">*</span> Para o filtro funcionar, todos os campos marcados com <span class="text-red-500 font-medium">*</span> devem ser preenchidos ou selecionados.
        </p>
        <div
            x-data="{
                tipo: '{{ request('tipo_relatorio', 'completo') }}',
                filtrosAplicados: false,
                filtrosAlterados: false,
                appliedParams: { data_unica: '', data_inicio: '', data_fim: '', local_ids: [] },
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
            @filtro-alterado.window="filtrosAlterados = true">
            <form method="GET" x-ref="formulario" @submit.prevent="filtrosAplicados = true; $nextTick(() => $refs.formulario.submit())"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo <span class="text-red-500">*</span></label>
                    <select name="tipo_relatorio" x-model="tipo" class="block w-full px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                        <option value="completo" {{ request('tipo_relatorio', 'completo') === 'completo' ? 'selected' : '' }}>Completo</option>
                        <option value="diario">Diário</option>
                        <option value="semanal">Por período</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div x-show="tipo === 'diario'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data <span class="text-red-500">*</span></label>
                    <input type="date" name="data_unica" value="{{ request('data_unica') }}" @change="filtrosAlterados = true" class="block w-full px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600" />
                </div>
                <template x-if="tipo === 'semanal'">
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Início <span class="text-red-500">*</span></label>
                            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" @change="filtrosAlterados = true" class="block w-full px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fim <span class="text-red-500">*</span></label>
                            <input type="date" name="data_fim" value="{{ request('data_fim') }}" @change="filtrosAlterados = true" class="block w-full px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600" />
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
                    @click.outside="open = false">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Local(is) <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div @click="open = !open" class="block w-full min-h-[2.5rem] px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600 flex flex-wrap items-center gap-1.5 cursor-pointer">
                            <template x-for="item in selected" :key="item.id">
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-sm text-slate-800 dark:bg-slate-800/90 dark:text-slate-200">
                                    <span x-text="item.label" class="truncate max-w-[200px]"></span>
                                    <button type="button" @click.stop="selected = selected.filter(s => s.id !== item.id); $dispatch('filtro-alterado')" class="leading-none hover:text-slate-950 dark:hover:text-white" aria-label="Remover">×</button>
                                </span>
                            </template>
                            <input x-show="open" x-model="search" @click.stop type="text" placeholder="Buscar local..." class="flex-1 min-w-[120px] bg-transparent border-0 p-0 text-sm placeholder-gray-500 focus:ring-0 focus:outline-none"
                                   @keydown.escape="open = false">
                            <span x-show="!open && selected.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">Selecione um Local</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-20 w-full mt-1 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 max-h-52 overflow-y-auto">
                            <template x-for="opt in filtered" :key="opt.id">
                                <div @click="toggle(opt)" role="option"
                                     class="flex items-center justify-between w-full px-3 py-2 text-sm cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
                                     :class="{ 'bg-slate-100 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100': isSelected(opt.id) }">
                                    <span x-text="opt.label" class="truncate flex-1"></span>
                                    <span x-show="isSelected(opt.id)" class="ml-2 text-slate-600 dark:text-slate-300">✓</span>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">Nenhum local encontrado</div>
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
                    @click.outside="open = false">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bairro(s)</label>
                    <div class="relative">
                        <div @click="open = !open" class="block w-full min-h-[2.5rem] px-3 py-2 rounded-lg bg-gray-50 text-gray-900 shadow-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 focus:border-blue-500 focus:ring-blue-500 dark:focus:border-blue-600 dark:focus:ring-blue-600 flex flex-wrap items-center gap-1.5 cursor-pointer focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-0">
                            <template x-for="val in selected" :key="val">
                                <span class="inline-flex items-center gap-1 rounded bg-slate-100 px-2 py-0.5 text-sm text-slate-800 dark:bg-slate-800/90 dark:text-slate-200">
                                    <span x-text="val"></span>
                                    <button type="button" @click.stop="selected = selected.filter(s => s !== val); $dispatch('filtro-alterado')" class="leading-none hover:text-slate-950 dark:hover:text-white" aria-label="Remover">×</button>
                                </span>
                            </template>
                            <input x-show="open" x-model="search" @click.stop type="text" placeholder="Buscar bairro..." class="flex-1 min-w-[120px] bg-transparent border-0 p-0 text-sm placeholder-gray-500 focus:ring-0 focus:outline-none"
                                   @keydown.escape="open = false">
                            <span x-show="!open && selected.length === 0" class="text-gray-500 dark:text-gray-400 text-sm">Selecione um ou mais bairros...</span>
                        </div>
                        <div x-show="open" x-transition
                             class="absolute z-20 w-full mt-1 rounded-md shadow-lg bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 max-h-52 overflow-y-auto">
                            <template x-for="opt in filtered" :key="opt">
                                <div @click="toggle(opt)" role="option" :aria-selected="selected.includes(opt)"
                                     class="px-3 py-2 cursor-pointer text-sm flex items-center justify-between hover:bg-gray-100 dark:hover:bg-gray-700"
                                     :class="{ 'bg-slate-100 dark:bg-slate-800/80 text-slate-900 dark:text-slate-100': selected.includes(opt) }">
                                    <span x-text="opt"></span>
                                    <span x-show="selected.includes(opt)" class="text-slate-600 dark:text-slate-300">✓</span>
                                </div>
                            </template>
                            <div x-show="filtered.length === 0" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-text="options.length === 0 ? 'Nenhum bairro cadastrado nos locais.' : 'Nenhum bairro encontrado para a busca.'"></div>
                        </div>
                    </div>
                    <template x-for="val in selected" :key="val">
                        <input type="hidden" :name="'bairro[]'" :value="val">
                    </template>
                </div>
                <div class="flex flex-col sm:flex-row gap-2 justify-end items-stretch sm:items-center">
                    <button type="submit" class="btn-acesso-principal inline-flex items-center justify-center px-4 py-2 text-white font-medium rounded-md shadow-sm transition h-10">Filtrar</button>
                    <a href="{{ route('gestor.relatorios.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-800 dark:text-gray-100 font-medium rounded-md transition hover:bg-gray-300 dark:hover:bg-gray-500 h-10">Limpar</a>
                </div>
            </form>
            <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                <p x-show="filtrosAlterados" x-cloak class="text-sm text-amber-600 dark:text-amber-400 mb-2 font-medium">
                    Você alterou os filtros. Clique em <strong>Filtrar</strong> antes de gerar o PDF para que o relatório use os dados corretos.
                </p>
                <button type="button" :disabled="!botaoAtivo"
                    @click.prevent="if (!botaoAtivo) {
                        let msg = filtrosAlterados ? 'Você alterou os filtros. Clique em Filtrar antes de gerar o PDF.' : '';
                        if (!msg && tipo === 'diario' && !appliedParams.data_unica) msg = 'Selecione a data para o relatório diário e clique em Filtrar.';
                        if (!msg && tipo === 'semanal' && (!appliedParams.data_inicio || !appliedParams.data_fim)) msg = 'Selecione as datas de início e fim e clique em Filtrar.';
                        if (!msg && tipo === 'individual' && (!appliedParams.local_ids || appliedParams.local_ids.length === 0)) msg = 'Selecione ao menos um local e clique em Filtrar.';
                        if (!msg) msg = 'Aplique os filtros antes de gerar o PDF.';
                        alert(msg);
                        return;
                    } gerarBase64Graficos();"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <x-heroicon-o-arrow-down-tray class="mr-2 h-5 w-5 shrink-0" />
                    Gerar relatório em PDF
                </button>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Filtre os dados e clique no botão para gerar o documento.</p>
            </div>
        </div>
    </section>

    {{-- Indicadores (estilo dashboard) --}}
    <section class="space-y-4">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Indicadores do período</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-clipboard-document-list class="mr-2 h-6 w-6 shrink-0 text-blue-600 dark:text-blue-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total de visitas</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalVisitas }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-clock class="mr-2 h-6 w-6 shrink-0 text-amber-500 dark:text-amber-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas com pendência</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $percentualPendencias }}% <span class="text-sm text-gray-500">({{ $totalComPendencia }})</span></p>
            </div>
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-building-office-2 class="mr-2 h-6 w-6 shrink-0 text-blue-500 dark:text-blue-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Depósitos eliminados</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalDepEliminados }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-check-circle class="mr-2 h-6 w-6 shrink-0 text-blue-500 dark:text-blue-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas com tratamento</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $visitasComTratamento }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-beaker class="mr-2 h-6 w-6 shrink-0 text-blue-500 dark:text-blue-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Coletas realizadas</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalComColeta }}</p>
            </div>
            <div class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
                <div class="flex items-center">
                    <x-heroicon-o-map-pin class="mr-2 h-6 w-6 shrink-0 text-gray-500 dark:text-gray-400" />
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bairro com mais ocorrências</h3>
                </div>
                <p class="mt-2 text-lg font-normal text-gray-900 dark:text-gray-100">{{ $bairroMaisFrequente ?: '-' }}</p>
            </div>
        </div>
    </section>

    {{-- Gráficos: apenas os 4 mais relevantes para o relatório --}}
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Análise visual</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:grid-rows-2 sm:auto-rows-fr">
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">Visitas por bairro</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoBairros" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoBairrosVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">Sem dados no período.</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">Doenças registradas</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoDoencas" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoDoencasVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">Nenhuma doença registrada.</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">Visitas por dia</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoDias" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoDiasVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">Sem dados no período.</p>
                </div>
            </div>
            <div class="flex flex-col min-h-[13rem]">
                <h3 class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1 shrink-0">Formas de tratamento</h3>
                <div class="flex-1 min-h-[12rem] bg-gray-50 dark:bg-gray-800/50 rounded-lg flex items-center justify-center overflow-hidden">
                    <canvas id="graficoTratamentos" width="400" height="168" class="max-w-full"></canvas>
                    <p id="graficoTratamentosVazio" class="hidden w-full h-full min-h-[12rem] flex items-center justify-center text-base text-gray-600 dark:text-gray-300 px-6 py-8 text-center m-0">Nenhum tratamento registrado.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Mapa de calor --}}
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Mapa de calor</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Concentração de visitas no território.</p>
        <div class="relative w-full h-64 rounded-lg border border-gray-200 dark:border-gray-600">
            <div id="mapa-calor" class="w-full h-full rounded-lg"></div>
            <p id="mapa-calor-vazio" class="hidden absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/80 rounded-lg m-0">Nenhuma visita com localização no período.</p>
        </div>
    </section>

    {{-- Tabela de visitas --}}
    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Visitas Registradas</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Código</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Data</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Pendência</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Local</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Atividade</th>
                    <th class="p-4 text-left align-bottom font-semibold text-gray-700 dark:text-gray-300">
                        <span class="block leading-tight">Profissional</span>
                        <span class="mt-0.5 block text-[10px] font-normal text-gray-500 dark:text-gray-400">ACE ou ACS</span>
                    </th>
                    <th class="p-4 text-center font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($visitasPaginated as $visita)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="p-4">
                            <span class="inline-block bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-gray-200 text-xs font-semibold px-2 py-1 rounded">
                                #{{ $visita->vis_id }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                            <div class="font-semibold">
                                {{ \Carbon\Carbon::parse($visita->vis_data)->format('d/m/Y') }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                {{ \Carbon\Carbon::parse($visita->vis_data)->translatedFormat('l') }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100">
                            @if($visita->vis_pendencias)
                                <span class="inline-block bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">
                                    Pendente
                                </span>

                                @php
                                    $revisitaPosterior = $visita->local->visitas()
                                        ->where('vis_data', '>', $visita->vis_data)
                                        ->orderBy('vis_data')
                                        ->first();
                                @endphp

                                @if($revisitaPosterior)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1 italic">
                                        Revisitado em {{ \Carbon\Carbon::parse($revisitaPosterior->vis_data)->format('d/m/Y') }}
                                    </div>
                                @endif
                            @else
                                <span class="inline-block bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-blue-900 dark:text-blue-300">
                                    Concluída
                                </span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                            <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Bairro/Localidade: {{ $visita->local->loc_bairro }} · Cód.: {{ $visita->local->loc_codigo_unico }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100" title="{{ \App\Helpers\MsTerminologia::atividadeNome($visita->vis_atividade) }}">
                            <span class="text-sm">{{ \App\Helpers\MsTerminologia::atividadeLabel($visita->vis_atividade) ?: 'Não informado' }}</span>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 whitespace-nowrap">
                            {{ $visita->usuario->use_nome ?? '-' }}
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('gestor.visitas.show', $visita) }}"
                               class="btn-acesso-principal inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-lg shadow-sm transition hover:opacity-95 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500/50 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
                               title="{{ __('Visualizar') }}"
                               aria-label="{{ __('Visualizar visita') }}">
                                <x-heroicon-o-eye class="h-4 w-4 shrink-0" />
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-600 dark:text-gray-400 italic">Nenhuma visita encontrada.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if(isset($visitasPaginated))
        <x-pagination-relatorio :paginator="$visitasPaginated" item-label="visitas" />
    @endif
    @if(isset($visitasPaginated) && $visitasPaginated->total() == 0)
        <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">Nenhuma visita no período.</p>
    @endif
</section>

</div>
    @endif

{{-- Scripts (apenas quando há visitas; estado vazio não carrega gráficos/mapa) --}}
@if(!($sem_visitas ?? false))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.heat/dist/leaflet-heat.js"></script>
<script src="https://unpkg.com/leaflet-image/leaflet-image.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
document.addEventListener('DOMContentLoaded', function() {
    const visitas = @json($visitasParaGraficos ?? []);

    const contagemPorBairro = {};
    const contagemPorDoenca = {};

    (Array.isArray(visitas) ? visitas : []).forEach(v => {
        const bairro = (v.local && v.local.loc_bairro) ? String(v.local.loc_bairro) : 'Desconhecido';
        contagemPorBairro[bairro] = (contagemPorBairro[bairro] || 0) + 1;
        (v.doencas || []).forEach(d => {
            const nome = (d && d.doe_nome) ? String(d.doe_nome) : 'N/I';
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
                datasets: [{ label: 'Visitas', data: dataBairro, backgroundColor: 'rgba(59, 130, 246, 0.6)', borderColor: 'rgba(59, 130, 246, 1)', borderWidth: 1 }]
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
        new Chart(elDoencas, {
            type: 'pie',
            data: {
                labels: labelsDoenca,
                datasets: [{ data: dataDoenca, backgroundColor: ['#60a5fa', '#f87171', '#34d399', '#fbbf24', '#a78bfa', '#fb7185', '#38bdf8', '#facc15', '#4ade80', '#f472b6'] }]
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

    // Mapa de calor
    const pontos = (Array.isArray(visitas) ? visitas : [])
        .map(v => {
            const lat = parseFloat(v?.local?.loc_latitude || 0);
            const lng = parseFloat(v?.local?.loc_longitude || 0);
            return lat !== 0 && lng !== 0 ? [lat, lng, 1] : null;
        })
        .filter(p => p !== null);

    // Cálculo do centro (média dos pontos válidos)
    let centroLat = -28.65, centroLng = -52.42; // fallback
    if (pontos.length > 0) {
        const total = pontos.length;
        centroLat = pontos.reduce((sum, p) => sum + p[0], 0) / total;
        centroLng = pontos.reduce((sum, p) => sum + p[1], 0) / total;
    }

    // Inicialização do mapa centralizado no centro calculado
    const mapa = L.map('mapa-calor').setView([centroLat, centroLng], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 18,
    }).addTo(mapa);

    if (pontos.length > 0) {
        L.heatLayer(pontos, {
            radius: 25,
            blur: 15,
            maxZoom: 17,
            gradient: { 0.2: 'blue', 0.4: 'lime', 0.6: 'yellow', 0.8: 'orange', 1.0: 'red' }
        }).addTo(mapa);
    } else {
        document.getElementById('mapa-calor-vazio').classList.remove('hidden');
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
            leafletImage(mapa, function(err, canvas) {
                if (err || !canvas) {
                    console.error("Erro ao capturar o mapa:", err);
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
                datasets: [{ label: 'Visitas', data: datasOrdenadas.map(d => contagemPorData[d]), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.2)', tension: 0.3, fill: true }]
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
            const forma = t.trat_forma ?? 'Indefinida';
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