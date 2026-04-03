@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas', 'url' => route('agente.visitas.index')], ['label' => 'Editar']]" />

    <section class="rounded-xl border border-gray-200/80 bg-white p-5 shadow-sm dark:border-gray-600 dark:bg-gray-800">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Visita</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize os dados da visita, incluindo local, data e possíveis doenças.
        </p>
    </section>

    <section class="rounded-xl border border-gray-200/80 bg-white p-6 shadow-sm dark:border-gray-600 dark:bg-gray-800 space-y-6">
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('agente.visitas.update', $visita) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dados Básicos</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vis_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data da Visita <span class="text-red-500">*</span></label>
                        <input type="date" name="vis_data" id="vis_data" value="{{ old('vis_data', optional($visita)->vis_data) }}" required
                            class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="vis_ciclo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo/Ano <span class="text-red-500">*</span></label>
                        <input type="text" name="vis_ciclo" id="vis_ciclo" value="{{ old('vis_ciclo', optional($visita)->vis_ciclo) }}" required
                            class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600"
                            placeholder="mm/aa">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    O ciclo deve ser informado no formato "mm/aa", por exemplo, "01/{{ now()->format('y') }}" para a primeira referência do ano de {{ now()->year }}.
                </p>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Localização</legend>
                <div class="col-span-1 sm:col-span-2"
                    x-data="{
                        open: false,
                        search: '{{ old('fk_local_id') ? '' : ($visita?->local->loc_codigo_unico . ' - ' . $visita?->local->loc_endereco) }}',
                        selectedId: '{{ old('fk_local_id', $visita->fk_local_id ?? '') }}',
                        locais: {{ Js::from($locais) }},
                        limparSelecao() {
                            if (!this.selectedId) return;
                            this.selectedId = '';
                            this.search = '';
                            this.open = true;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    }"
                    x-init="$watch('search', () => open = true)">

                    <label for="fk_local_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local Visitado <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @click="limparSelecao" x-ref="input" placeholder="Buscar local..." required
                            class="block w-full px-3 py-2 rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">

                        <ul x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow max-h-60 overflow-auto"
                            x-transition>
                            <template x-for="local in locais.filter(l => {
                                const q = (search || '').toLowerCase();
                                if (!q) return true;
                                const end = (l.loc_endereco || '').toLowerCase();
                                const bairro = (l.loc_bairro || '').toLowerCase();
                                const cidade = (l.loc_cidade || '').toLowerCase();
                                const cod = String(l.loc_codigo_unico || '');
                                return end.includes(q) || bairro.includes(q) || cidade.includes(q) || cod.includes(q);
                            })" :key="local.loc_id">
                                <li>
                                    <button type="button"
                                            @click="selectedId = local.loc_id; search = 'Cód. ' + (local.loc_codigo_unico || '') + ' - ' + (local.loc_endereco || '') + ', ' + (local.loc_numero ?? 'S/N') + ' - ' + (local.loc_bairro || '') + ', ' + (local.loc_cidade || '') + '/' + (local.loc_estado || ''); open = false"
                                            class="block text-left w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <span x-text="'Cód. ' + (local.loc_codigo_unico || '') + ' - ' + (local.loc_endereco || '') + ', ' + (local.loc_numero ?? 'S/N') + ' - ' + (local.loc_bairro || '') + ', ' + (local.loc_cidade || '') + '/' + (local.loc_estado || '')"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <input type="hidden" name="fk_local_id" :value="selectedId">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Se o local visitado não estiver na lista, você pode adicioná-lo na seção de <a href="{{ route('agente.locais.create') }}" class="text-gray-800 hover:underline">locais</a>.
                </p>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Atividades</legend>
                <div>
                    <label for="vis_atividade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Atividade <span class="text-red-500">*</span></label>
                    <select id="vis_atividade" name="vis_atividade" required
                            class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                        <option value="">Selecione...</option>
                        @foreach(config('ms_terminologia.atividades_pncd') as $cod => $at)
                            <option value="{{ $cod }}" {{ old('vis_atividade', $visita->vis_atividade ?? '') == $cod ? 'selected' : '' }}>{{ $at['codigo'] }} | {{ $at['nome'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Atividades conforme Diretrizes Nacionais (MS). LIRAa (7) é método simplificado de vigilância entomológica.
                    </p>
                </div>
            </fieldset>

            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                <div>
                    <label for="vis_visita_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo da visita</label>
                    <select name="vis_visita_tipo" id="vis_visita_tipo"
                            class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                        <option value="">Selecione...</option>
                        @foreach(config('ms_terminologia.visita_tipo') as $tipoVal => $tipoConf)
                            <option value="{{ $tipoVal }}" {{ old('vis_visita_tipo', $visita->vis_visita_tipo ?? '') == $tipoVal ? 'selected' : '' }}>{{ $tipoConf['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Depósitos Inspecionados</legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (['a1', 'a2', 'b', 'c', 'd1', 'd2', 'e'] as $tipo)
                        <div>
                            <label for="insp_{{ $tipo }}" class="block text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Depósito {{ strtoupper($tipo) }}</label>
                            <input id="insp_{{ $tipo }}" name="insp_{{ $tipo }}" type="number" min="0" value="{{ old('insp_' . $tipo, optional($visita)->{'insp_' . $tipo}) }}"
                                class="mt-1 block w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Os depósitos são classificados como:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <p>A1 – Depósitos elevados com água (caixas d’água, tambores).</p>
                        <p>A2 – Depósitos ao nível do solo (cisternas, filtros, barris).</p>
                        <p>B – Pequenos móveis (vasos, pratos, pingadeiras, fontes).</p>
                        <p>C – Fixos ou de difícil remoção (calhas, ralos, lajes).</p>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <p>D1 – Pneus e materiais rodantes.</p>
                        <p>D2 – Lixo, sucatas e entulho.</p>
                        <p>E – Naturais (oclusões em árvores, folhas, rochas).</p>
                    </div>
                </div>
                <div>
                    <label for="vis_depositos_eliminados" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Depósitos Eliminados</label>
                    <input type="number" name="vis_depositos_eliminados" id="vis_depositos_eliminados" min="0" value="{{ old('vis_depositos_eliminados', optional($visita)->vis_depositos_eliminados) }}"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Coleta de Amostra</legend>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="vis_coleta_amostra" id="vis_coleta_amostra" value="1" {{ old('vis_coleta_amostra', optional($visita)->vis_coleta_amostra) ? 'checked' : '' }}
                        class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_coleta_amostra" class="text-sm text-gray-700 dark:text-gray-300">Houve coleta de amostra?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pb-4">
                    Se marcado, você poderá informar o número de amostra inicial e final, além da quantidade de tubitos utilizados.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vis_amos_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Inicial</label>
                        <input type="number" name="vis_amos_inicial" id="vis_amos_inicial" min="0" value="{{ old('vis_amos_inicial', optional($visita)->vis_amos_inicial) }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                    <div>
                        <label for="vis_amos_final" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Final</label>
                        <input type="number" name="vis_amos_final" id="vis_amos_final" min="0" value="{{ old('vis_amos_final', optional($visita)->vis_amos_final) }}"
                            class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                    </div>
                </div>
                <div>
                    <label for="vis_qtd_tubitos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade de Tubitos Utilizados</label>
                    <input type="number" name="vis_qtd_tubitos" id="vis_qtd_tubitos" min="0" value="{{ old('vis_qtd_tubitos', optional($visita)->vis_qtd_tubitos) }}"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">
                </div>
            </fieldset>

            {{-- Tratamentos --}}
            <div x-data="{ exibirTratamentos: {{ old('tratamentos') || ($visita->tratamentos && count($visita->tratamentos)) ? 'true' : 'false' }}, tratamentos: {{ old('tratamentos', json_encode($visita->tratamentos ?? [])) }} }" class="space-y-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tratamentos Realizados</label>

                <template x-if="tratamentos.length === 0">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 rounded">
                        Nenhum tratamento foi informado. Caso tenha realizado algum, clique no botão abaixo.
                        <div class="mt-2">
                            <button type="button"
                                    @click="exibirTratamentos = true; tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                    class="btn-acesso-principal px-3 py-1 text-white text-sm font-semibold rounded shadow">
                                + Adicionar Tratamento
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="exibirTratamentos">
                    <div class="space-y-4">
                        <template x-for="(t, i) in tratamentos" :key="i">
                            <div class="p-4 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Forma</label>
                                        <select :name="`tratamentos[${i}][trat_forma]`"
                                                x-model="t.trat_forma"
                                                @change="
                                                    if (t.trat_forma === 'Focal') {
                                                        t.trat_tipo = 'Larvicida';
                                                        t.qtd_cargas = null;
                                                    } else {
                                                        t.trat_tipo = 'Adulticida';
                                                        t.linha = '';
                                                        t.qtd_gramas = null;
                                                        t.qtd_depositos_tratados = null;
                                                    }
                                                "
                                                class="mt-1 w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                            <option value="Focal">Focal</option>
                                            <option value="Perifocal">Perifocal</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Tipo</label>
                                        <input type="text" :name="`tratamentos[${i}][trat_tipo]`" x-model="t.trat_tipo" readonly
                                            class="mt-1 block w-full rounded-lg border border-gray-300 bg-gray-200 text-gray-900 shadow-sm cursor-not-allowed dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100">
                                    </div>
                                    <div x-show="t.trat_forma === 'Focal'">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Linha</label>
                                        <select :name="`tratamentos[${i}][linha]`" x-model="t.linha"
                                                class="mt-1 w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="t.trat_forma === 'Focal'">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Quantidade (gramas)</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_gramas]`" x-model="t.qtd_gramas"
                                            class="mt-1 block w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Depósitos Tratados</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_depositos_tratados]`" x-model="t.qtd_depositos_tratados"
                                            class="mt-1 block w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="t.trat_forma === 'Perifocal'">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Quantidade de Cargas</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_cargas]`" x-model="t.qtd_cargas"
                                            class="mt-1 block w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                    </div>
                                </div>

                                <div class="flex justify-end">
                                    <button type="button" @click="tratamentos.splice(i, 1)"
                                            class="px-2 py-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold rounded shadow">
                                        - Remover Tratamento
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-start" x-show="tratamentos.length > 0">
                            <button type="button"
                                    @click="tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                    class="btn-acesso-principal px-2 py-1 text-white font-semibold text-xs rounded shadow">
                                + Adicionar Tratamento
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Campo oculto com os dados serializados -->
                <template x-if="exibirTratamentos">
                    <input type="hidden" name="tratamentos" :value="JSON.stringify(tratamentos)">
                </template>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Para um tratamento ser considerado válido e adicionado na base de dados, é necessário preencher todos os campos relacionados.
                </p>
            </div>

            @if($sugestoesDisponiveis ?? false)
            {{-- Sugestões de doenças (JS puro) --}}
            <div id="sugestoes-doencas-wrap" class="space-y-2" data-sugestoes-url="{{ route('agente.sugestoes-doencas') }}" data-local-id="{{ $visita->fk_local_id }}">
                <div class="flex flex-wrap gap-2 items-center">
                    <button type="button" id="btn-sugestoes-doencas" class="text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-2 hover:text-slate-900 dark:text-slate-400 dark:decoration-slate-600 dark:hover:text-slate-200">
                        Ver sugestões de doenças para este imóvel
                    </button>
                </div>
                <div id="sugestoes-doencas-result" class="space-y-2" style="display:none;">
                    <div id="sugestoes-doencas-loading" class="text-sm text-gray-500 dark:text-gray-400">Buscando…</div>
                    <div id="sugestoes-doencas-erro" class="text-sm text-red-600 dark:text-red-400" style="display:none;"></div>
                    <div id="sugestoes-doencas-empty" class="text-sm text-gray-500 dark:text-gray-400" style="display:none;">Nenhuma sugestão para este imóvel.</div>
                    <div id="sugestoes-doencas-list" class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-600 space-y-3" style="display:none;">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">Sugestões com base nos dados já cadastrados</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">O sistema sugere doenças com base no histórico do imóvel, nas visitas do município e nas palavras que você escreveu nas observações.</p>
                        <div id="sugestoes-doencas-buttons" class="flex flex-wrap gap-2 items-center"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Doenças Monitoradas --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Possíveis doenças</legend>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($doencas as $doenca)
                        <div class="flex items-center">
                            <input type="checkbox"
                                id="doenca_{{ $doenca->doe_id }}"
                                name="doencas[]"
                                value="{{ $doenca->doe_id }}"
                                {{ in_array($doenca->doe_id, old('doencas', $visita->doencas->pluck('doe_id')->toArray())) ? 'checked' : '' }}
                                class="mr-2 text-blue-600 dark:text-blue-400">
                            <label for="doenca_{{ $doenca->doe_id }}" class="text-sm text-gray-700 dark:text-gray-300">
                                {{ $doenca->doe_nome }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Marque as possíveis doenças relacionadas à visita, se houver. Por exemplo, se há algum morador no local com suspeita de dengue.
                </p>
            </fieldset>

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pendências</label>
                <div class="flex items-center pt-4">
                    <input type="checkbox" name="vis_pendencias" id="vis_pendencias" value="1" {{ old('vis_pendencias', optional($visita)->vis_pendencias) ? 'checked' : '' }}
                        class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_pendencias" class="text-sm text-gray-700 dark:text-gray-300">Houve alguma pendência na visita?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pb-4">
                    Se marcado, você pode informar as pendências no campo de observações abaixo.
                </p>
            </div>

            <div>
                <label for="vis_observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                <textarea name="vis_observacoes" id="vis_observacoes" rows="5"
                        class="mt-1 w-full rounded-lg border border-gray-200 bg-gray-50 text-gray-900 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-100 dark:focus:border-blue-600 dark:focus:ring-blue-600">{{ old('vis_observacoes', optional($visita)->vis_observacoes) }}</textarea>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Utilize este campo para registrar observações adicionais sobre a visita, como condições encontradas, dificuldades enfrentadas ou recomendações.
            </p>

            <div class="flex justify-end">
                <button type="submit"
                        class="btn-acesso-principal px-6 py-2 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Atualizar Visita
                </button>
            </div>
        </form>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var wrap = document.getElementById('sugestoes-doencas-wrap');
    if (!wrap) return;
    var btn = document.getElementById('btn-sugestoes-doencas');
    var result = document.getElementById('sugestoes-doencas-result');
    var loading = document.getElementById('sugestoes-doencas-loading');
    var erroEl = document.getElementById('sugestoes-doencas-erro');
    var emptyEl = document.getElementById('sugestoes-doencas-empty');
    var listWrap = document.getElementById('sugestoes-doencas-list');
    var buttonsWrap = document.getElementById('sugestoes-doencas-buttons');
    btn.addEventListener('click', function() {
        result.style.display = '';
        loading.style.display = '';
        erroEl.style.display = 'none';
        emptyEl.style.display = 'none';
        listWrap.style.display = 'none';
        var url = wrap.getAttribute('data-sugestoes-url');
        var localId = wrap.getAttribute('data-local-id') || (document.querySelector('input[name="fk_local_id"]') || {}).value || '';
        var obs = (document.getElementById('vis_observacoes') || {}).value || '';
        var fullUrl = url + '?local_id=' + encodeURIComponent(localId) + '&observacoes=' + encodeURIComponent(obs);
        fetch(fullUrl)
            .then(function(r) {
                if (!r.ok) throw new Error('Erro ' + r.status);
                return r.json();
            })
            .then(function(data) {
                var sugestoes = data.sugestoes || [];
                loading.style.display = 'none';
                if (sugestoes.length === 0) {
                    emptyEl.style.display = '';
                    return;
                }
                buttonsWrap.innerHTML = '';
                sugestoes.forEach(function(s) {
                    var bt = document.createElement('button');
                    bt.type = 'button';
                    bt.className = 'inline-flex items-center gap-1 rounded-full border border-slate-200 bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-800 hover:bg-slate-200 dark:border-slate-600 dark:bg-slate-800/90 dark:text-slate-200 dark:hover:bg-slate-700';
                    bt.title = s.motivo || '';
                    bt.textContent = s.nome || '';
                    bt.addEventListener('click', function() {
                        var cb = document.getElementById('doenca_' + s.doe_id);
                        if (cb && !cb.checked) cb.checked = true;
                    });
                    buttonsWrap.appendChild(bt);
                });
                listWrap.style.display = 'block';
            })
            .catch(function(e) {
                loading.style.display = 'none';
                erroEl.textContent = e.message || 'Falha ao carregar.';
                erroEl.style.display = '';
            });
    });
});
</script>
@endsection