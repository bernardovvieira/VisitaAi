@extends('layouts.app')

@section('content')
<!-- Overlay de carregamento -->
<div id="overlayCarregando" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex items-center justify-center z-50 hidden">
    <div class="text-center">
        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-white mx-auto mb-4"></div>
        <p class="text-white text-lg font-semibold">Gerando relatório, aguarde...</p>
    </div>
</div>

<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Relatórios e Indicadores</h1>

    @if(session('error'))
        <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded relative mb-4">
            <strong>Erro:</strong> {{ session('error') }}
        </div>
    @endif
    @if(session('info'))
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded relative mb-4">
            {{ session('info') }}
        </div>
    @endif

    @if($sem_visitas ?? false)
        {{-- Estado vazio: nenhuma visita no sistema — sem ações disponíveis --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow px-8 py-12 text-center">
            <div class="max-w-md mx-auto pt-12 pb-12">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-2">Nenhuma visita cadastrada</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Não há visitas no sistema. Os relatórios, indicadores e a geração de PDF ficarão disponíveis após o cadastro de visitas pelos agentes.
                </p>
                <a href="{{ route('gestor.visitas.index') }}" class="btn-acesso-principal inline-flex items-center px-4 py-2 text-white font-medium rounded-lg transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7-3.732 7-9.542 7-8.268-2.943-9.542-7z"/></svg>
                    Ir para Visitas
                </a>
            </div>
        </div>
    @else
    {{-- Filtros e PDF --}}
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Filtros e relatório</h2>
        <div
            x-data="{
                tipo: '{{ request('tipo_relatorio', 'completo') }}',
                filtrosAplicados: new URLSearchParams(window.location.search).toString() !== '',
                get botaoAtivo() { return this.tipo === 'completo' || this.filtrosAplicados; }
            }"
            x-init="$watch('tipo', () => filtrosAplicados = false)">
            <form method="GET" x-ref="formulario" @submit.prevent="filtrosAplicados = true; $nextTick(() => $refs.formulario.submit())"
                  class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 items-end">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipo</label>
                    <select name="tipo_relatorio" x-model="tipo" class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600">
                        <option value="completo" {{ request('tipo_relatorio', 'completo') === 'completo' ? 'selected' : '' }}>Completo</option>
                        <option value="diario">Diário</option>
                        <option value="semanal">Por período</option>
                        <option value="individual">Individual</option>
                    </select>
                </div>
                <div x-show="tipo === 'diario'" x-cloak>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Data</label>
                    <input type="date" name="data_unica" value="{{ request('data_unica') }}" class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600" />
                </div>
                <template x-if="tipo === 'semanal'">
                    <div class="md:col-span-2 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Início</label>
                            <input type="date" name="data_inicio" value="{{ request('data_inicio') }}" class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Fim</label>
                            <input type="date" name="data_fim" value="{{ request('data_fim') }}" class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600" />
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Local(is)</label>
                    <div class="relative">
                        <div @click="open = !open" class="block w-full min-h-[2.5rem] px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600 flex flex-wrap items-center gap-1.5 cursor-pointer">
                            <template x-for="item in selected" :key="item.id">
                                <span class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded px-2 py-0.5 text-sm">
                                    <span x-text="item.label" class="truncate max-w-[200px]"></span>
                                    <button type="button" @click.stop="selected = selected.filter(s => s.id !== item.id)" class="hover:text-blue-600 dark:hover:text-blue-100 leading-none" aria-label="Remover">×</button>
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
                                     :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200': isSelected(opt.id) }">
                                    <span x-text="opt.label" class="truncate flex-1"></span>
                                    <span x-show="isSelected(opt.id)" class="text-blue-600 dark:text-blue-400 ml-2">✓</span>
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
                        <div @click="open = !open" class="block w-full min-h-[2.5rem] px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm border border-gray-300 dark:border-gray-600 flex flex-wrap items-center gap-1.5 cursor-pointer focus-within:ring-2 focus-within:ring-blue-500 focus-within:ring-offset-0">
                            <template x-for="val in selected" :key="val">
                                <span class="inline-flex items-center gap-1 bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 rounded px-2 py-0.5 text-sm">
                                    <span x-text="val"></span>
                                    <button type="button" @click.stop="selected = selected.filter(s => s !== val)" class="hover:text-blue-600 dark:hover:text-blue-100 leading-none" aria-label="Remover">×</button>
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
                                     :class="{ 'bg-blue-50 dark:bg-blue-900/30 text-blue-800 dark:text-blue-200': selected.includes(opt) }">
                                    <span x-text="opt"></span>
                                    <span x-show="selected.includes(opt)" class="text-blue-600 dark:text-blue-400">✓</span>
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
                <button type="button" :disabled="!botaoAtivo"
                    @click.prevent="if (!botaoAtivo) { alert('Aplique os filtros antes de gerar o PDF.'); return; } gerarBase64Graficos();"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-medium rounded-md shadow-sm disabled:opacity-50 disabled:cursor-not-allowed transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-indigo-500 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Total de visitas</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalVisitas }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-amber-500 dark:text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas com pendência</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $percentualPendencias }}% <span class="text-sm text-gray-500">({{ $totalComPendencia }})</span></p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-emerald-500 dark:text-emerald-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Depósitos eliminados</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalDepEliminados }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-teal-500 dark:text-teal-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Visitas com tratamento</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $visitasComTratamento }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Coletas realizadas</h3>
                </div>
                <p class="mt-2 text-2xl font-normal text-gray-900 dark:text-gray-100">{{ $totalComColeta }}</p>
            </div>
            <div class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
                <div class="flex items-center">
                    <svg class="h-6 w-6 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200">Bairro com mais ocorrências</h3>
                </div>
                <p class="mt-2 text-lg font-normal text-gray-900 dark:text-gray-100">{{ $bairroMaisFrequente ?: '—' }}</p>
            </div>
        </div>
    </section>

    {{-- Gráficos: apenas os 4 mais relevantes para o relatório --}}
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
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
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-1">Mapa de calor</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Concentração de visitas no território.</p>
        <div class="relative w-full h-64 rounded-lg border border-gray-200 dark:border-gray-600">
            <div id="mapa-calor" class="w-full h-full rounded-lg"></div>
            <p id="mapa-calor-vazio" class="hidden absolute inset-0 flex items-center justify-center text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/80 rounded-lg m-0">Nenhuma visita com localização no período.</p>
        </div>
    </section>

    {{-- Tabela de visitas --}}
    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
    <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Visitas Registradas</h2>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 rounded-lg shadow text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700">
                <tr>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Código</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Data</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Pendência</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Local</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Atividade</th>
                    <th class="p-4 text-left font-semibold text-gray-700 dark:text-gray-300">Agente</th>
                    <th class="p-4 text-center font-semibold text-gray-700 dark:text-gray-300">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($visitas as $visita)
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
                                <span class="inline-block bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">
                                    Concluída
                                </span>
                            @endif
                        </td>
                        @php
                            $atividades = [
                                '1' => 'LI',
                                '2' => 'LI+T',
                                '3' => 'PPE+T',
                                '4' => 'T',
                                '5' => 'DF',
                                '6' => 'PVE',
                                '7' => 'LIRAa',
                                '8' => 'PE',
                            ];
                        @endphp
                        <td class="p-4 text-gray-800 dark:text-gray-100 leading-tight">
                            <div class="font-semibold">{{ $visita->local->loc_endereco }}, {{ $visita->local->loc_numero }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                Bairro/Localidade: {{ $visita->local->loc_bairro }} — Cód.: {{ $visita->local->loc_codigo_unico }}
                            </div>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100">
                            <span class="text-sm">{{ $atividades[$visita->vis_atividade] ?? 'Não informado' }}</span>
                        </td>
                        <td class="p-4 text-gray-800 dark:text-gray-100 whitespace-nowrap">
                            {{ $visita->usuario->use_nome ?? '—' }}
                        </td>
                        <td class="p-4 text-center">
                            <a href="{{ route('gestor.visitas.show', $visita) }}"
                               class="btn-acesso-principal inline-flex items-center px-3 py-1.5 text-xs font-semibold text-white rounded shadow transition">
                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                     viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7-3.732 7-9.542 7-8.268-2.943-9.542-7z"/>
                                </svg>
                                Visualizar
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

        // Filtros do formulário
        const dataInicio = document.querySelector('[name="data_inicio"]')?.value || '';
        const dataFim = document.querySelector('[name="data_fim"]')?.value || '';
        const bairroInputs = document.querySelectorAll('input[name="bairro[]"]');
        const bairros = Array.from(bairroInputs).map(inp => inp.value);
        const tipoRelatorio = document.querySelector('[name="tipo_relatorio"]')?.value || 'completo';
        const dataUnica = document.querySelector('[name="data_unica"]')?.value || '';
        
        addField('data_unica', dataUnica);
        addField('tipo_relatorio', tipoRelatorio);
        addField('data_inicio', dataInicio);
        addField('data_fim', dataFim);
        bairros.forEach(function(b) { addField('bairro[]', b); });

        if (tipoRelatorio === 'individual') {
            document.querySelectorAll('input[name="local_id[]"]').forEach(function(inp) {
                addField('local_id[]', inp.value);
            });
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
                datasets: [{ data: dataTrat, backgroundColor: ['#3b82f6', '#10b981', '#facc15', '#f87171', '#a78bfa'] }]
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