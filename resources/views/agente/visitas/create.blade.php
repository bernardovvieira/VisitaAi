@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto space-y-6">
    <x-breadcrumbs :items="[['label' => 'Página Inicial', 'url' => route('dashboard')], ['label' => 'Visitas', 'url' => route('agente.visitas.index')], ['label' => 'Cadastrar']]" />
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
        <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Registrar visita</h2>
        <p class="mt-2 text-gray-600 dark:text-gray-400">
            Preencha os dados da visita e dos tratamentos realizados.
        </p>
        <p class="mt-3 text-sm text-gray-600 dark:text-gray-400">
            <strong>Sem internet?</strong> Use o botão "Guardar no dispositivo para enviar depois" no final do formulário. A visita fica salva só no seu aparelho. Quando tiver conexão, abra o menu Visitas e clique em "Enviar visitas salvas no dispositivo" para enviar todas de uma vez.<br>
            <b>Nota:</b> Antes de ir a campo, abra a tela de registrar visita pelo menos uma vez com internet para o sistema guardar a página e funcionar offline.
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

        <form method="POST" action="{{ route('agente.visitas.store') }}" class="space-y-6"
            x-data="{ carregando: false }"
            x-on:submit="carregando = true">

            @csrf

            {{-- Dados básicos --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Dados Básicos</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vis_data" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Data da Visita <span class="text-red-500">*</span></label>
                        <input type="date" name="vis_data" id="vis_data" value="{{ old('vis_data', now()->toDateString()) }}" required
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="vis_ciclo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ciclo/Ano <span class="text-red-500">*</span></label>
                        <input type="text" name="vis_ciclo" id="vis_ciclo" value="{{ old('vis_ciclo') }}" required
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm"
                            placeholder="mm/aa">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    O ciclo deve ser informado no formato "mm/aa", por exemplo, "01/{{ now()->format('y') }}" para a primeira referência do ano de {{ now()->year }}.
                </p>
            </fieldset>

            {{-- Localização --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Localização</legend>
                <div class="col-span-1 sm:col-span-2"
                    x-data="{
                        open: false,
                        search: '',
                        selectedId: {{ old('fk_local_id') ?? 'null' }},
                        locais: {{ Js::from($locais) }},
                        limparSelecao() {
                            if (!this.selectedId) return;
                            this.selectedId = '';
                            this.search = '';
                            this.open = true;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    }"
                    x-init="
                        $watch('search', () => open = true);
                        window.addEventListener('visita-apply-draft-local', function(e) {
                            var p = e.detail;
                            if (p && p.fk_local_id && locais && locais.length) {
                                var loc = locais.find(function(l) { return l.loc_id == p.fk_local_id; });
                                if (loc) {
                                    selectedId = loc.loc_id;
                                    search = 'Cód. ' + (loc.loc_codigo_unico || '') + ' - ' + (loc.loc_endereco || '') + ', ' + (loc.loc_numero ?? 'S/N') + ' - ' + (loc.loc_bairro || '') + ', ' + (loc.loc_cidade || '') + '/' + (loc.loc_estado || '');
                                    open = false;
                                }
                            }
                        });
                    ">

                    <label for="fk_local_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Local Visitado <span class="text-red-500">*</span></label>
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @click="limparSelecao" x-ref="input" placeholder="Buscar local..." required
                                class="block w-full px-3 py-2 rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">

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
                                    <button type="button" @click="selectedId = Number(local.loc_id); search = 'Cód. ' + (local.loc_codigo_unico || '') + ' - ' + (local.loc_endereco || '') + ', ' + (local.loc_numero ?? 'S/N') + ' - ' + (local.loc_bairro || '') + ', ' + (local.loc_cidade || '') + '/' + (local.loc_estado || ''); open = false"
                                            class="block text-left w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-100 hover:bg-gray-100 dark:hover:bg-gray-700">
                                        <span x-text="'Cód. ' + (local.loc_codigo_unico || '') + ' - ' + (local.loc_endereco || '') + ', ' + (local.loc_numero ?? 'S/N') + ' - ' + (local.loc_bairro || '') + ', ' + (local.loc_cidade || '') + '/' + (local.loc_estado || '')">
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <input type="hidden" name="fk_local_id" x-bind:value="selectedId"> 
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
                            class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Selecione...</option>
                        @foreach(config('ms_terminologia.atividades_pncd') as $cod => $at)
                            <option value="{{ $cod }}" {{ old('vis_atividade') == $cod ? 'selected' : '' }}>{{ $at['codigo'] }} — {{ $at['nome'] }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Atividades conforme Diretrizes Nacionais (MS). LIRAa (7) é método simplificado de vigilância entomológica.
                    </p>
                </div>
            </fieldset>

            {{-- Tipo da visita --}}
            <div class="grid grid-cols-1 sm:grid-cols-1 gap-4">
                <div>
                    <label for="vis_visita_tipo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tipo da visita</label>
                    <select name="vis_visita_tipo" id="vis_visita_tipo"
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        <option value="">Selecione...</option>
                        @foreach(config('ms_terminologia.visita_tipo') as $tipoVal => $tipoConf)
                            <option value="{{ $tipoVal }}" {{ old('vis_visita_tipo') == $tipoVal ? 'selected' : '' }}>{{ $tipoConf['label'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Depósitos Inspecionados --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Depósitos Inspecionados</legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (['a1', 'a2', 'b', 'c', 'd1', 'd2', 'e'] as $tipo)
                        <div>
                            <label for="insp_{{ $tipo }}" class="block text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">Depósito {{ strtoupper($tipo) }}</label>
                            <input id="insp_{{ $tipo }}" name="insp_{{ $tipo }}" type="number" min="0" value="{{ old('insp_' . $tipo) }}"
                                   class="mt-1 block w-full rounded-md bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Os depósitos são classificados como:
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <p>A1– Depósitos elevados com água (caixas d’água, tambores).</p>    
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
                    <input type="number" name="vis_depositos_eliminados" id="vis_depositos_eliminados" min="0" value="{{ old('vis_depositos_eliminados') }}"
                           class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
            </fieldset>
            
            {{-- Tubitos e amostra --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2" style="padding-bottom: 1rem;">Coleta de Amostra</legend>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="vis_coleta_amostra" id="vis_coleta_amostra" value="1" {{ old('vis_coleta_amostra') ? 'checked' : '' }}
                        class="mr-2 text-green-600 dark:text-green-400">
                    <label for="vis_coleta_amostra" class="text-sm text-gray-700 dark:text-gray-300">Houve coleta de amostra?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" style="padding-bottom: 1rem;">
                    Se marcado, você poderá informar o número de amostra inicial e final, além da quantidade de tubitos utilizados.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="vis_amos_inicial" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Inicial</label>
                        <input type="number" name="vis_amos_inicial" id="vis_amos_inicial" min="0" value="{{ old('vis_amos_inicial') }}" disabled
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                    <div>
                        <label for="vis_amos_final" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Número de Amostra Final</label>
                        <input type="number" name="vis_amos_final" id="vis_amos_final" min="0" value="{{ old('vis_amos_final') }}" disabled
                            class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                    </div>
                </div>
                <div>
                    <label for="vis_qtd_tubitos" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Quantidade de Tubitos Utilizados</label>
                    <input type="number" name="vis_qtd_tubitos" id="vis_qtd_tubitos" min="0" value="{{ old('vis_qtd_tubitos') }}" disabled
                        class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Se a coleta de amostra for realizada, informe os números de amostra inicial e final, além da quantidade de tubitos utilizados.
                </p>
            </fieldset>

            {{-- Tratamentos --}}
            <div x-data="{ exibirTratamentos: {{ old('tratamentos') ? 'true' : 'false' }}, tratamentos: {{ old('tratamentos', '[]') }} }"
                 x-init="window.addEventListener('visita-apply-draft-tratamentos', function(e) { tratamentos = (e.detail && Array.isArray(e.detail)) ? e.detail : []; exibirTratamentos = tratamentos.length > 0; })"
                 class="space-y-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tratamentos Realizados</label>

                <div x-show="tratamentos.length === 0" class="p-4 bg-gray-50 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 rounded">
                    Nenhum tratamento foi informado. Caso tenha realizado algum, clique no botão abaixo.
                    <div class="mt-2">
                        <button type="button"
                                @click="exibirTratamentos = true; tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                class="btn-acesso-principal px-3 py-1 text-white text-sm font-semibold rounded shadow">
                            + Adicionar Tratamento
                        </button>
                    </div>
                </div>

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
                                            <option value="Focal" selected>Focal</option>
                                            <option value="Perifocal">Perifocal</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">Tipo</label>
                                        <input type="text" :name="`tratamentos[${i}][trat_tipo]`" x-model="t.trat_tipo" readonly
                                            :value="t.trat_tipo.charAt(0).toUpperCase() + t.trat_tipo.slice(1)"
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
                                <!-- Botão para remover o tratamento -->
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
            {{-- Sugestões de doenças (JS puro para evitar escape no HTML) --}}
            <div id="sugestoes-doencas-wrap" class="space-y-2" data-sugestoes-url="{{ route('agente.sugestoes-doencas') }}">
                <div class="flex flex-wrap gap-2 items-center">
                    <button type="button" id="btn-sugestoes-doencas" class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline">
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

            {{-- Possíveis doenças --}}
            <fieldset class="space-y-3">
                <legend class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Possíveis doenças</legend>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach ($doencas as $doenca)
                        <div class="flex items-center">
                            <input type="checkbox"
                                id="doenca_{{ $doenca->doe_id }}"
                                name="doencas[]"
                                value="{{ $doenca->doe_id }}"
                                {{ in_array($doenca->doe_id, old('doencas', [])) ? 'checked' : '' }}
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

            {{-- Pendências --}}
            <div class="space-y-3">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pendências</label>
                <div class="flex items-center" style="padding-top: 1rem;">
                    <input type="checkbox" name="vis_pendencias" id="vis_pendencias" value="1" {{ old('vis_pendencias') ? 'checked' : '' }}
                           class="mr-2 text-green-600 dark:text-green-400">
                    <label for="vis_pendencias" class="text-sm text-gray-700 dark:text-gray-300">Houve alguma pendência na visita?</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" style="padding-bottom: 1rem;">
                    Se marcado, você pode informar as pendências no campo de observações abaixo.
                </p>

            {{-- Observações --}}
            <div>
                <label for="vis_observacoes" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Observações</label>
                <textarea name="vis_observacoes" id="vis_observacoes" rows="5"
                          class="mt-1 w-full rounded bg-gray-100 dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm">{{ old('vis_observacoes') }}</textarea>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Utilize este campo para registrar observações adicionais sobre a visita, como condições encontradas, dificuldades enfrentadas ou recomendações.
            </p>

            <div class="border-t border-gray-200 dark:border-gray-600 pt-6 space-y-3">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    O sistema detecta se você está com internet. Ao clicar no botão, a visita será <span id="visita-online-desc">registrada na hora</span> ou guardada no dispositivo para enviar depois.
                </p>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" id="btn-registrar-visita"
                            class="px-6 py-2 font-semibold text-sm rounded-lg shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed"
                            data-online-class="bg-green-600 hover:bg-green-700 text-white"
                            data-offline-class="bg-amber-500 hover:bg-amber-600 text-amber-900">
                        Registrar visita
                    </button>
                    <span id="visita-online-status" class="text-sm text-gray-500 dark:text-gray-400"></span>
                </div>
            </div>
        </form>
    </section>
</div>

<!-- mascara ciclo -mm/aa com barra e sugestões de doenças -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var wrap = document.getElementById('sugestoes-doencas-wrap');
        if (wrap) {
            var btn = document.getElementById('btn-sugestoes-doencas');
            var result = document.getElementById('sugestoes-doencas-result');
            var loading = document.getElementById('sugestoes-doencas-loading');
            var erroEl = document.getElementById('sugestoes-doencas-erro');
            var emptyEl = document.getElementById('sugestoes-doencas-empty');
            var listWrap = document.getElementById('sugestoes-doencas-list');
            var buttonsWrap = document.getElementById('sugestoes-doencas-buttons');
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                var localId = (document.querySelector('input[name="fk_local_id"]') || {}).value || '';
                if (!localId) {
                    erroEl.textContent = 'Selecione o local visitado antes de buscar sugestões.';
                    erroEl.style.display = '';
                    result.style.display = '';
                    loading.style.display = 'none';
                    listWrap.style.display = 'none';
                    emptyEl.style.display = 'none';
                    return;
                }
                result.style.display = '';
                loading.style.display = '';
                erroEl.style.display = 'none';
                emptyEl.style.display = 'none';
                listWrap.style.display = 'none';
                var url = wrap.getAttribute('data-sugestoes-url');
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
                            bt.className = 'inline-flex items-center gap-1 px-2.5 py-1 text-xs font-medium rounded-full bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800/50 border border-blue-200 dark:border-blue-700';
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
        }

        const cicloInput = document.getElementById('vis_ciclo');
        cicloInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 4);
            if (this.value.length >= 2) {
                this.value = this.value.slice(0, 2) + '/' + this.value.slice(2);
            }
        });
    });

    document.getElementById('vis_coleta_amostra').addEventListener('change', function() {
        const amostraInicial = document.getElementById('vis_amos_inicial');
        const amostraFinal = document.getElementById('vis_amos_final');
        const qtdTubitos = document.getElementById('vis_qtd_tubitos');

        if (this.checked) {
            amostraInicial.disabled = false;
            amostraFinal.disabled = false;
            qtdTubitos.disabled = false;
        } else {
            amostraInicial.disabled = true;
            amostraFinal.disabled = true;
            qtdTubitos.disabled = true;
        }
    });

    (function() {
        var btn = document.getElementById('btn-registrar-visita');
        var desc = document.getElementById('visita-online-desc');
        var statusEl = document.getElementById('visita-online-status');
        if (!btn || !desc || !statusEl) return;

        function updateVisitaOnlineUI() {
            var online = navigator.onLine;
            var baseClass = 'px-6 py-2 font-semibold text-sm rounded-lg shadow-md transition disabled:opacity-50 disabled:cursor-not-allowed ';
            if (online) {
                btn.textContent = 'Registrar visita';
                btn.className = baseClass + (btn.getAttribute('data-online-class') || 'bg-green-600 hover:bg-green-700 text-white');
                desc.textContent = 'registrada na hora';
                statusEl.textContent = 'Com internet';
            } else {
                btn.textContent = 'Guardar visita';
                btn.className = baseClass + (btn.getAttribute('data-offline-class') || 'bg-amber-500 hover:bg-amber-600 text-amber-900');
                desc.textContent = 'guardada no dispositivo para enviar depois';
                statusEl.textContent = 'Sem internet';
            }
        }

        function saveDraft() {
            var form = document.querySelector('form[action="{{ route('agente.visitas.store') }}"]');
            if (!form) return Promise.reject();
            var localId = form.querySelector('input[name="fk_local_id"]');
            var localIdVal = localId ? parseInt(localId.value, 10) : 0;
            if (!localIdVal) {
                alert('Selecione o local visitado antes de salvar.');
                return Promise.reject();
            }
            var payload = {
                vis_data: (form.querySelector('input[name="vis_data"]') || {}).value || '',
                vis_ciclo: (form.querySelector('input[name="vis_ciclo"]') || {}).value || '',
                fk_local_id: localIdVal,
                vis_atividade: (form.querySelector('select[name="vis_atividade"]') || {}).value || '',
                vis_visita_tipo: (form.querySelector('select[name="vis_visita_tipo"]') || {}).value || '',
                vis_observacoes: (form.querySelector('textarea[name="vis_observacoes"]') || {}).value || '',
                vis_pendencias: (form.querySelector('input[name="vis_pendencias"]') || {}).checked ? 1 : 0,
                vis_coleta_amostra: (form.querySelector('input[name="vis_coleta_amostra"]') || {}).checked ? 1 : 0,
                vis_amos_inicial: parseInt((form.querySelector('input[name="vis_amos_inicial"]') || {}).value || 0, 10) || null,
                vis_amos_final: parseInt((form.querySelector('input[name="vis_amos_final"]') || {}).value || 0, 10) || null,
                vis_qtd_tubitos: parseInt((form.querySelector('input[name="vis_qtd_tubitos"]') || {}).value || 0, 10) || null,
                vis_depositos_eliminados: parseInt((form.querySelector('input[name="vis_depositos_eliminados"]') || {}).value || 0, 10) || null
            };
            ['insp_a1','insp_a2','insp_b','insp_c','insp_d1','insp_d2','insp_e'].forEach(function(name) {
                var el = form.querySelector('input[name="' + name + '"]');
                payload[name] = el ? (parseInt(el.value, 10) || null) : null;
            });
            payload.doencas = [];
            form.querySelectorAll('input[name="doencas[]"]:checked').forEach(function(cb) { payload.doencas.push(parseInt(cb.value, 10)); });
            var tratInput = form.querySelector('input[name="tratamentos"]');
            payload.tratamentos = (tratInput && tratInput.value) ? (function(){ try { return JSON.parse(tratInput.value); } catch (e) { return []; } })() : [];
            if (typeof window.VisitaOfflineSaveDraft !== 'function') {
                alert('Recurso de offline não disponível. Tente recarregar a página.');
                return Promise.reject();
            }
            return window.VisitaOfflineSaveDraft('agente', payload);
        }

        updateVisitaOnlineUI();
        document.addEventListener('visita-connection-change', function() { updateVisitaOnlineUI(); });

        btn.addEventListener('click', function() {
            if (btn.disabled) return;
            if (navigator.onLine) {
                btn.disabled = true;
                document.querySelector('form[action="{{ route('agente.visitas.store') }}"]').submit();
            } else {
                btn.disabled = true;
                saveDraft().then(function() {
                    if (window.VisitaOfflineUpdateBanner) window.VisitaOfflineUpdateBanner();
                    alert('Visita guardada no dispositivo. Quando tiver internet, vá em Visitas e clique em "Enviar visitas salvas no dispositivo" para enviar.');
                    window.location.href = '{{ route('agente.visitas.index') }}';
                }).catch(function() {}).finally(function() { btn.disabled = false; });
            }
        });
    })();

    (function loadDraftFromUrl() {
        var params = new URLSearchParams(window.location.search || '');
        var draftId = params.get('draft');
        if (!draftId || typeof window.VisitaOfflineGetDraft !== 'function') return;
        function apply() {
        window.VisitaOfflineGetDraft(draftId).then(function(d) {
            if (!d || !d.payload) return;
            var p = d.payload;
            var form = document.querySelector('form[action="{{ route('agente.visitas.store') }}"]');
            if (!form) return;
            function setVal(name, val, isCheckbox) {
                var el = form.querySelector('[name="' + name + '"]');
                if (!el) return;
                if (isCheckbox) { el.checked = !!val; return; }
                el.value = val != null ? String(val) : '';
            }
            setVal('vis_data', p.vis_data);
            setVal('vis_ciclo', p.vis_ciclo);
            setVal('vis_atividade', p.vis_atividade);
            setVal('vis_visita_tipo', p.vis_visita_tipo);
            setVal('vis_observacoes', p.vis_observacoes);
            setVal('vis_pendencias', p.vis_pendencias, true);
            setVal('vis_coleta_amostra', p.vis_coleta_amostra, true);
            setVal('vis_amos_inicial', p.vis_amos_inicial);
            setVal('vis_amos_final', p.vis_amos_final);
            setVal('vis_qtd_tubitos', p.vis_qtd_tubitos);
            setVal('vis_depositos_eliminados', p.vis_depositos_eliminados);
            ['insp_a1','insp_a2','insp_b','insp_c','insp_d1','insp_d2','insp_e'].forEach(function(name) { setVal(name, p[name]); });
            (p.doencas || []).forEach(function(doeId) {
                var cb = form.querySelector('input[name="doencas[]"][value="' + doeId + '"]');
                if (cb) cb.checked = true;
            });
            window.dispatchEvent(new CustomEvent('visita-apply-draft-local', { detail: p }));
            window.dispatchEvent(new CustomEvent('visita-apply-draft-tratamentos', { detail: p.tratamentos || [] }));
        }).catch(function() {});
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function() { setTimeout(apply, 80); });
        else setTimeout(apply, 80);
    })();
</script>
@endsection