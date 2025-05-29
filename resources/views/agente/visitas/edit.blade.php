@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="container mx-auto p-6 max-w-4xl space-y-6">
    <div>
        <a href="{{ route('agente.visitas.index') }}"
           class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold text-sm rounded-lg shadow transition">
            <svg class="h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Voltar
        </a>
    </div>

    <section class="p-4 bg-white dark:bg-gray-700 rounded-lg shadow">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Editar Visita</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Atualize os dados da visita, incluindo local, data e doenças detectadas.
        </p>
    </section>

    <section class="p-6 bg-white dark:bg-gray-700 rounded-lg shadow space-y-6">
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
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="vis_ciclo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo/Ano <span class="text-red-500">*</span></label>
                        <input type="text" name="vis_ciclo" id="vis_ciclo" value="{{ old('vis_ciclo', optional($visita)->vis_ciclo) }}" required
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm"
                            placeholder="mm/aa">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    O ciclo deve ser informado no formato "mm/aa", por exemplo, "01/25" para janeiro de 2025.
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
                            class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">

                        <ul x-show="open" @click.away="open = false" class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow max-h-60 overflow-auto">
                            <template x-for="local in locais.filter(l => {
                                const query = search.toLowerCase();
                                return (
                                    l.loc_endereco.toLowerCase().includes(query) ||
                                    l.loc_bairro.toLowerCase().includes(query) ||
                                    l.loc_codigo_unico.toString().includes(query)
                                );
                            })" :key="local.loc_id">
                                <li>
                                    <button type="button"
                                            @click="selectedId = local.loc_id; search = 'Cód. ' + local.loc_codigo_unico + ' - ' + local.loc_endereco + ', ' + local.loc_numero + ' - ' + local.loc_bairro + ', ' + local.loc_cidade + '/' + local.loc_estado; open = false"
                                            class="block text-left w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <span x-text="'Cód. ' + local.loc_codigo_unico + ' - ' + local.loc_endereco + ', ' + local.loc_numero + ' - ' + local.loc_bairro + ', ' + local.loc_cidade + '/' + local.loc_estado"></span>
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
                    <label for="vis_atividade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Atividade PNCD <span class="text-red-500">*</span></label>
                    <select id="vis_atividade" name="vis_atividade" required
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Selecione...</option>
                        <option value="1" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '1' ? 'selected' : '' }}>1 - LI (Levantamento de Índice)</option>
                        <option value="2" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '2' ? 'selected' : '' }}>2 - LI+T (Levantamento + Tratamento)</option>
                        <option value="3" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '3' ? 'selected' : '' }}>3 - PPE+T (Ponto Estratégico + Tratamento)</option>
                        <option value="4" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '4' ? 'selected' : '' }}>4 - T (Tratamento)</option>
                        <option value="5" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '5' ? 'selected' : '' }}>5 - DF (Delimitação de Foco)</option>
                        <option value="6" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '6' ? 'selected' : '' }}>6 - PVE (Pesquisa Vetorial Especial)</option>
                        <option value="7" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '7' ? 'selected' : '' }}>7 - LIRAa (Levantamento de Índice Rápido)</option>
                        <option value="8" {{ old('vis_atividade', $visita->vis_atividade ?? '') == '8' ? 'selected' : '' }}>8 - PE (Ponto Estratégico)</option>
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        O tipo "7 - LIRAa" é um tipo especial e não faz parte da tabela original PNCD.
                    </p>
                </div>
            </fieldset>

            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                <div>
                    <label for="vis_visita_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo da Visita PNCD</label>
                    <select name="vis_visita_tipo" id="vis_visita_tipo"
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Selecione...</option>
                        <option value="N" {{ old('vis_visita_tipo', $visita->vis_visita_tipo ?? '') == 'N' ? 'selected' : '' }}>Normal</option>
                        <option value="R" {{ old('vis_visita_tipo', $visita->vis_visita_tipo ?? '') == 'R' ? 'selected' : '' }}>Recuperação</option>
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
                                class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
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
                        class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Coleta de Amostra</legend>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="vis_coleta_amostra" id="vis_coleta_amostra" value="1" {{ old('vis_coleta_amostra', optional($visita)->vis_coleta_amostra) ? 'checked' : '' }}
                        class="mr-2 text-green-600 dark:text-green-400">
                    <label for="vis_coleta_amostra" class="text-sm text-gray-700 dark:text-gray-300">Houve coleta de amostra?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pb-4">
                    Se marcado, você poderá informar o número de amostra inicial e final, além da quantidade de tubitos utilizados.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vis_amos_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Inicial</label>
                        <input type="number" name="vis_amos_inicial" id="vis_amos_inicial" min="0" value="{{ old('vis_amos_inicial', optional($visita)->vis_amos_inicial) }}"
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="vis_amos_final" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Final</label>
                        <input type="number" name="vis_amos_final" id="vis_amos_final" min="0" value="{{ old('vis_amos_final', optional($visita)->vis_amos_final) }}"
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="vis_qtd_tubitos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade de Tubitos Utilizados</label>
                    <input type="number" name="vis_qtd_tubitos" id="vis_qtd_tubitos" min="0" value="{{ old('vis_qtd_tubitos', optional($visita)->vis_qtd_tubitos) }}"
                        class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
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
                                    class="px-3 py-1 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded shadow">
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
                                            class="mt-1 block w-full rounded bg-gray-200 dark:bg-gray-600 text-gray-900 dark:text-gray-100 shadow-sm cursor-not-allowed">
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
                                    class="px-2 py-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs rounded shadow">
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

            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pendências</label>
                <div class="flex items-center pt-4">
                    <input type="checkbox" name="vis_pendencias" id="vis_pendencias" value="1" {{ old('vis_pendencias', optional($visita)->vis_pendencias) ? 'checked' : '' }}
                        class="mr-2 text-green-600 dark:text-green-400">
                    <label for="vis_pendencias" class="text-sm text-gray-700 dark:text-gray-300">Houve alguma pendência na visita?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pb-4">
                    Se marcado, você pode informar as pendências no campo de observações abaixo.
                </p>
            </div>

            <div>
                <label for="vis_observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                <textarea name="vis_observacoes" id="vis_observacoes" rows="5"
                        class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">{{ old('vis_observacoes', optional($visita)->vis_observacoes) }}</textarea>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Utilize este campo para registrar observações adicionais sobre a visita, como condições encontradas, dificuldades enfrentadas ou recomendações.
            </p>

            <div class="flex justify-end">
                <button type="submit"
                        class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold text-sm rounded-lg shadow-md transition">
                    Atualizar Visita
                </button>
            </div>
        </form>
    </section>
</div>
@endsection