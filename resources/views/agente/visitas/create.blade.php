@extends('layouts.app')

@section('og_title', config('app.brand') . ' · ' . __('Registrar visita'))
@section('og_description', __('Registro em campo de visita de vigilância entomológica e controle vetorial.'))

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection

@section('content')
<div class="v-page space-y-5">
    @include('visitas.partials._form-js-strings')
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Visitas'), 'url' => route('agente.visitas.index')], ['label' => __('Cadastrar')]]" />

    <x-page-header :eyebrow="__('Registro em campo')" :title="__('Registrar visita')">
        <x-slot name="lead">
            <p class="text-sm">{{ __('Preencha os dados da visita e dos tratamentos.') }}</p>
        </x-slot>
    </x-page-header>

    <x-flash-alerts />

    <x-section-card class="v-card--tight v-card--muted border border-sky-200/70 bg-gradient-to-br from-sky-50/90 to-white dark:border-sky-900/40 dark:from-slate-900/70 dark:to-slate-900/40">
        <div class="flex items-start gap-2.5">
            <span class="mt-0.5 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/40 dark:text-sky-300">
                <x-heroicon-o-wifi class="h-3.5 w-3.5" />
            </span>
            <div class="min-w-0">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                    {{ __('Modo offline') }}
                </h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-600 dark:text-slate-300">
                    {{ __('Salve a visita no dispositivo quando estiver sem internet e finalize o envio na sincronização quando houver conexão.') }}
                </p>
            </div>
        </div>
    </x-section-card>

    <x-section-card class="space-y-6 dark:bg-gray-800">
        @if ($errors->any())
            <x-alert type="error" :title="__('Corrija os erros nos campos indicados abaixo.')" :message="implode(' ', $errors->all())" />
        @endif

        <form method="POST" action="{{ route('agente.visitas.store') }}" class="space-y-6"
            x-data="{ carregando: false }"
            x-on:submit="carregando = true">

            @csrf

            {{-- Dados básicos --}}
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Dados básicos') }}</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field name="vis_data" :label="__('Data da visita')" :required="true">
                        <x-text-input type="date" name="vis_data" id="vis_data" value="{{ old('vis_data', now()->toDateString()) }}" required />
                    </x-form-field>
                    <x-form-field name="vis_ciclo" :label="__('Ciclo/ano')" :required="true">
                        <x-text-input type="text" name="vis_ciclo" id="vis_ciclo" value="{{ old('vis_ciclo') }}" required placeholder="{{ __('mm/aa') }}" />
                    </x-form-field>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('O ciclo deve ser informado no formato mm/aa, por exemplo, :exemplo para a primeira referência do ano de :ano.', ['exemplo' => '"01/'.now()->format('y').'"', 'ano' => (string) now()->year]) }}
                </p>
            </fieldset>

            {{-- Localização --}}
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Localização') }}</legend>
                <div class="col-span-1 sm:col-span-2"
                    x-data="{
                        open: false,
                        search: '',
                        selectedId: {{ old('fk_local_id') ?? 'null' }},
                        selectedDraftId: '',
                        locais: {{ Js::from($locais) }},
                        oldMoradorObs: @js((object) old('morador_obs', [])),
                        locaisDraft: [],
                        limparSelecao() {
                            this.selectedId = '';
                            this.selectedDraftId = '';
                            this.search = '';
                            this.open = true;
                            this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
                        }
                    }"
                    x-init="
                        $watch('search', () => open = true);
                        if (typeof window.VisitaOfflineGetLocalDrafts === 'function') {
                            window.VisitaOfflineGetLocalDrafts('agente').then(function(drafts) {
                                $el.dispatchEvent(new CustomEvent('local-drafts-loaded', { detail: drafts || [], bubbles: false }));
                            });
                            $el.addEventListener('local-drafts-loaded', function(e) { locaisDraft = e.detail || []; });
                        }
                        window.addEventListener('visita-apply-draft-local', function(e) {
                            var p = e.detail;
                            if (p && p.local_draft_id) {
                                selectedDraftId = p.local_draft_id;
                                selectedId = '';
                                search = window.__visitaFormStrings.deviceLocalPending;
                                open = false;
                            } else if (p && p.fk_local_id && locais && locais.length) {
                                var loc = locais.find(function(l) { return l.loc_id == p.fk_local_id; });
                                if (loc) {
                                    selectedId = loc.loc_id;
                                    selectedDraftId = '';
                                    search = window.__visitaFormatLocalLine(loc);
                                    open = false;
                                }
                            }
                        });
                    ">

                    <x-input-label for="fk_local_id" :value="__('Local visitado')" class="mb-2" />
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @click="limparSelecao" x-ref="input" placeholder="{{ __('Buscar local...') }}" required
                                class="v-input">

                        <ul x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow max-h-60 overflow-auto"
                            x-transition>
                            <template x-if="locaisDraft && locaisDraft.length">
                                <li class="px-2 py-1.5 text-xs font-semibold text-amber-700 dark:text-amber-300 border-b border-gray-200 dark:border-gray-600">{{ __('Locais no dispositivo') }}</li>
                            </template>
                            <template x-for="d in locaisDraft" :key="d.id">
                                <li>
                                    <button type="button" @click="selectedDraftId = d.id; selectedId = ''; search = window.__visitaFormStrings.deviceDraftPrefix + (d.payload && d.payload.loc_endereco || d.id); open = false"
                                            class="block text-left w-full px-3 py-2 text-sm text-amber-800 dark:text-amber-200 hover:bg-amber-50 dark:hover:bg-amber-900/30">
                                        <span x-text="(d.payload && d.payload.loc_endereco) || d.id"></span>
                                    </button>
                                </li>
                            </template>
                            <template x-if="locais && locais.length">
                                <li class="px-2 py-1.5 text-xs font-semibold text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-600">{{ __('Locais cadastrados') }}</li>
                            </template>
                            <template x-for="local in locais.filter(l => {
                                const normalize = (value) => (value || '').toString().toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '');
                                const q = normalize(search);
                                if (!q) return true;
                                const fields = [
                                    l.loc_endereco,
                                    l.loc_numero,
                                    l.loc_complemento,
                                    l.loc_bairro,
                                    l.loc_cidade,
                                    l.loc_estado,
                                    l.loc_pais,
                                    l.loc_cep,
                                    l.loc_codigo_unico,
                                    l.loc_categoria,
                                    l.loc_responsavel_nome,
                                    l.loc_lado,
                                ];
                                return fields.some(field => normalize(field).includes(q));
                            })" :key="local.loc_id">
                                <li>
                                    <button type="button" @click="selectedId = Number(local.loc_id); selectedDraftId = ''; search = window.__visitaFormatLocalLine(local); open = false"
                                            class="v-list-item-hover block w-full px-3 py-2 text-left text-sm text-gray-700 dark:text-gray-100">
                                        <span x-text="window.__visitaFormatLocalLine(local)">
                                        </span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <input type="hidden" name="fk_local_id" x-bind:value="selectedDraftId ? '' : selectedId">
                    <input type="hidden" name="local_draft_id" x-bind:value="selectedDraftId">

                    <div class="mt-4 space-y-3 border-t border-gray-200 pt-4 dark:border-gray-600" x-show="selectedId && !selectedDraftId" x-cloak>
                        <p class="v-section-title text-sm">{{ __('Ocupantes desta visita') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Abra apenas o ocupante que precisar observar.') }}</p>
                        <template x-for="m in ((locais.find(l => Number(l.loc_id) === Number(selectedId)) || {}).moradores) || []" :key="m.mor_id">
                            <details class="rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
                                <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2 text-sm font-medium text-gray-800 dark:text-gray-100 [&::-webkit-details-marker]:hidden">
                                    <span x-text="(m.mor_nome && m.mor_nome.trim()) ? m.mor_nome : ('{{ __('Ocupante') }} #' + m.mor_id)"></span>
                                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ __('Abrir observação') }}</span>
                                </summary>
                                <div class="border-t border-gray-200 px-3 pb-3 pt-2 dark:border-gray-700">
                                    <textarea
                                        class="v-input mt-1"
                                        rows="2"
                                        x-bind:name="'morador_obs[' + m.mor_id + ']'"
                                        x-init="$el.value = (oldMoradorObs[String(m.mor_id)] ?? oldMoradorObs[m.mor_id] ?? '')"
                                        placeholder="{{ __('Informações sobre este ocupante nesta visita') }}"
                                    ></textarea>
                                </div>
                            </details>
                        </template>
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="selectedId && !selectedDraftId && (!((locais.find(l => Number(l.loc_id) === Number(selectedId)) || {}).moradores) || !((locais.find(l => Number(l.loc_id) === Number(selectedId)) || {}).moradores).length)">
                            {{ __('Nenhum ocupante cadastrado para este imóvel.') }}
                        </p>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {!! __('Se o local visitado não estiver na lista, você pode adicioná-lo na seção de :link.', ['link' => '<a href="'.e(route('agente.locais.create')).'" class="text-gray-800 hover:underline">'.__('locais').'</a>']) !!}
                </p>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Atividades') }}</legend>
                <x-form-field name="vis_atividade" :label="__('Atividade')" :required="true" :help="__('Atividades conforme Diretrizes Nacionais (MS). LIRAa (7) é método simplificado de vigilância entomológica.')">
                    <select id="vis_atividade" name="vis_atividade" required
                            class="v-select mt-1">
                        <option value="">{{ __('Selecione…') }}</option>
                        @foreach(config('ms_terminologia.atividades_pncd') as $cod => $at)
                            <option value="{{ $cod }}" {{ old('vis_atividade') == $cod ? 'selected' : '' }}>{{ $at['codigo'] }} · {{ __($at['nome']) }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </fieldset>

            {{-- Tipo da visita --}}
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Tipo da visita') }}</legend>
                <x-form-field name="vis_visita_tipo" :label="__('Tipo da visita')">
                    <select name="vis_visita_tipo" id="vis_visita_tipo"
                            class="v-select mt-1">
                        <option value="">{{ __('Selecione…') }}</option>
                        @foreach(config('ms_terminologia.visita_tipo') as $tipoVal => $tipoConf)
                            <option value="{{ $tipoVal }}" {{ old('vis_visita_tipo') == $tipoVal ? 'selected' : '' }}>{{ __($tipoConf['label']) }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </fieldset>

            {{-- Depósitos Inspecionados --}}
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Depósitos inspecionados') }}</legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (['a1', 'a2', 'b', 'c', 'd1', 'd2', 'e'] as $tipo)
                        <div>
                            <label for="insp_{{ $tipo }}" class="block text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">{{ __('Depósito :tipo', ['tipo' => strtoupper($tipo)]) }}</label>
                            <input id="insp_{{ $tipo }}" name="insp_{{ $tipo }}" type="number" min="0" value="{{ old('insp_' . $tipo) }}"
                                   class="v-input mt-1">
                        </div>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Os depósitos são classificados como:') }}
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-2 gap-4">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <p>{{ __('A1: depósitos elevados com água (caixas d’água, tambores).') }}</p>
                        <p>{{ __('A2: depósitos ao nível do solo (cisternas, filtros, barris).') }}</p>
                        <p>{{ __('B: pequenos móveis (vasos, pratos, pingadeiras, fontes).') }}</p>
                        <p>{{ __('C: fixos ou de difícil remoção (calhas, ralos, lajes).') }}</p>
                    </div>
                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        <p>{{ __('D1: pneus e materiais rodantes.') }}</p>
                        <p>{{ __('D2: lixo, sucatas e entulho.') }}</p>
                        <p>{{ __('E: naturais (oclusões em árvores, folhas, rochas).') }}</p>
                    </div>
                </div>
                <x-form-field name="vis_depositos_eliminados" :label="__('Depósitos eliminados')">
                    <x-text-input type="number" name="vis_depositos_eliminados" id="vis_depositos_eliminados" min="0" value="{{ old('vis_depositos_eliminados') }}" />
                </x-form-field>
            </fieldset>
            
            {{-- Tubitos e amostra --}}
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Coleta de amostra') }}</legend>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="vis_coleta_amostra" id="vis_coleta_amostra" value="1" {{ old('vis_coleta_amostra') ? 'checked' : '' }}
                        class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_coleta_amostra" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Houve coleta de amostra?') }}</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Se marcado, informe as amostras e a quantidade de tubitos usados.') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field name="vis_amos_inicial" :label="__('Número de amostra inicial')">
                        <x-text-input type="number" name="vis_amos_inicial" id="vis_amos_inicial" min="0" value="{{ old('vis_amos_inicial') }}" disabled />
                    </x-form-field>
                    <x-form-field name="vis_amos_final" :label="__('Número de amostra final')">
                        <x-text-input type="number" name="vis_amos_final" id="vis_amos_final" min="0" value="{{ old('vis_amos_final') }}" disabled />
                    </x-form-field>
                </div>
                <x-form-field name="vis_qtd_tubitos" :label="__('Quantidade de tubitos utilizados')">
                    <x-text-input type="number" name="vis_qtd_tubitos" id="vis_qtd_tubitos" min="0" value="{{ old('vis_qtd_tubitos') }}" disabled />
                </x-form-field>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Se a coleta de amostra for realizada, informe os números de amostra inicial e final, além da quantidade de tubitos utilizados.') }}
                </p>
            </fieldset>

            {{-- Tratamentos --}}
            <div x-data="{ exibirTratamentos: {{ old('tratamentos') ? 'true' : 'false' }}, tratamentos: {{ old('tratamentos', '[]') }} }"
                 x-init="window.addEventListener('visita-apply-draft-tratamentos', function(e) { tratamentos = (e.detail && Array.isArray(e.detail)) ? e.detail : []; exibirTratamentos = tratamentos.length > 0; })"
                 class="space-y-4">
                <x-input-label :value="__('Tratamentos')" class="mb-2" />

                <div x-show="tratamentos.length === 0" class="p-4 bg-gray-50 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 rounded">
                    {{ __('Nenhum tratamento foi informado. Adicione se necessário.') }}
                    <div class="mt-2">
                        <button type="button"
                                @click="exibirTratamentos = true; tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                class="v-btn-compact v-btn-compact--blue">
                            {{ __('+ Adicionar tratamento') }}
                        </button>
                    </div>
                </div>

                <template x-if="exibirTratamentos">
                    <div class="space-y-4">
                        <template x-for="(t, i) in tratamentos" :key="i">
                            <div class="p-4 border border-gray-300 dark:border-gray-600 rounded-md bg-gray-50 dark:bg-gray-800 space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Forma') }}</label>
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
                                            <option value="Focal" selected>{{ __('Focal') }}</option>
                                            <option value="Perifocal">{{ __('Perifocal') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tipo') }}</label>
                                        <input type="text" :name="`tratamentos[${i}][trat_tipo]`" x-model="t.trat_tipo" readonly
                                            :value="t.trat_tipo.charAt(0).toUpperCase() + t.trat_tipo.slice(1)"
                                            class="v-input mt-1 cursor-not-allowed bg-slate-200/90 opacity-90 dark:bg-slate-800 dark:text-slate-100">
                                    </div>
                                    <div x-show="t.trat_forma === 'Focal'">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Linha') }}</label>
                                        <select :name="`tratamentos[${i}][linha]`" x-model="t.linha"
                                                class="mt-1 w-full rounded bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 shadow-sm">
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="t.trat_forma === 'Focal'">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Quantidade (gramas)') }}</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_gramas]`" x-model="t.qtd_gramas"
                                            class="v-input mt-1">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Depósitos tratados') }}</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_depositos_tratados]`" x-model="t.qtd_depositos_tratados"
                                            class="v-input mt-1">
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" x-show="t.trat_forma === 'Perifocal'">
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Quantidade de cargas') }}</label>
                                        <input type="number" min="0" :name="`tratamentos[${i}][qtd_cargas]`" x-model="t.qtd_cargas"
                                            class="v-input mt-1">
                                    </div>
                                </div>
                                <!-- Botão para remover o tratamento -->
                                <div class="flex justify-end">
                                    <button type="button" @click="tratamentos.splice(i, 1)"
                                            class="v-btn-compact v-btn-compact--red">
                                        {{ __('− Remover tratamento') }}
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div class="flex justify-start" x-show="tratamentos.length > 0">
                            <button type="button"
                                    @click="tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                    class="v-btn-compact v-btn-compact--blue">
                                {{ __('+ Adicionar tratamento') }}
                            </button>
                        </div>
                    </div>
                </template>

                <!-- Campo oculto com os dados serializados -->
                <template x-if="exibirTratamentos">
                    <input type="hidden" name="tratamentos" :value="JSON.stringify(tratamentos)">
                </template>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Para um tratamento ser considerado válido e adicionado na base de dados, é necessário preencher todos os campos relacionados.') }}
                </p>
            </div>

            @if($sugestoesDisponiveis ?? false)
            {{-- Sugestões de doenças (JS puro para evitar escape no HTML) --}}
            <div id="sugestoes-doencas-wrap" class="space-y-2" data-sugestoes-url="{{ route('agente.sugestoes-doencas') }}">
                <div class="flex flex-wrap gap-2 items-center">
                    <button type="button" id="btn-sugestoes-doencas" class="text-sm font-medium text-slate-600 underline decoration-slate-300 underline-offset-2 hover:text-slate-900 dark:text-slate-400 dark:decoration-slate-600 dark:hover:text-slate-200">
                        {{ __('Ver sugestões de doenças para este imóvel') }}
                    </button>
                </div>
                <div id="sugestoes-doencas-result" class="space-y-2" style="display:none;">
                    <div id="sugestoes-doencas-loading" class="text-sm text-gray-500 dark:text-gray-400">{{ __('Buscando…') }}</div>
                    <div id="sugestoes-doencas-erro" class="text-sm text-red-600 dark:text-red-400" style="display:none;"></div>
                    <div id="sugestoes-doencas-empty" class="text-sm text-gray-500 dark:text-gray-400" style="display:none;">{{ __('Nenhuma sugestão para este imóvel.') }}</div>
                    <div id="sugestoes-doencas-list" class="p-4 bg-slate-50 dark:bg-slate-800/50 rounded-lg border border-slate-200 dark:border-slate-600 space-y-3" style="display:none;">
                        <p class="text-sm font-medium text-gray-800 dark:text-gray-200">{{ __('Sugestões com base nos dados já cadastrados') }}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">{{ __('O sistema sugere doenças com base no histórico do imóvel, nas visitas do município e nas palavras que você escreveu nas observações.') }}</p>
                        <div id="sugestoes-doencas-buttons" class="flex flex-wrap gap-2 items-center"></div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Possíveis doenças --}}
            @php
                $vDoencaTones = [
                    'rounded-xl border border-amber-200/90 bg-amber-50/50 p-2.5 dark:border-amber-800/45 dark:bg-amber-950/20',
                    'rounded-xl border border-sky-200/90 bg-sky-50/50 p-2.5 dark:border-sky-800/45 dark:bg-sky-950/20',
                    'rounded-xl border border-violet-200/90 bg-violet-50/50 p-2.5 dark:border-violet-800/45 dark:bg-violet-950/20',
                    'rounded-xl border border-emerald-200/90 bg-emerald-50/50 p-2.5 dark:border-emerald-800/45 dark:bg-emerald-950/20',
                    'rounded-xl border border-rose-200/90 bg-rose-50/50 p-2.5 dark:border-rose-800/45 dark:bg-rose-950/20',
                ];
            @endphp
            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Possíveis doenças') }}</legend>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($doencas as $doenca)
                        <div class="{{ $vDoencaTones[$loop->index % 5] }} flex min-h-[2.75rem] items-center gap-2.5">
                            <input type="checkbox"
                                id="doenca_{{ $doenca->doe_id }}"
                                name="doencas[]"
                                value="{{ $doenca->doe_id }}"
                                {{ in_array($doenca->doe_id, old('doencas', [])) ? 'checked' : '' }}
                                class="shrink-0 text-blue-600 dark:text-blue-400">
                            <label for="doenca_{{ $doenca->doe_id }}" class="flex-1 cursor-pointer text-sm font-medium text-slate-800 dark:text-slate-100">
                                {{ $doenca->doe_nome }}
                            </label>
                        </div>
                    @endforeach
                </div>

                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Marque as possíveis doenças relacionadas à visita, se houver. Por exemplo, se há algum morador no local com suspeita de dengue.') }}
                </p>
            </fieldset>

            {{-- Pendências --}}
            <div class="space-y-3">
                <x-input-label :value="__('Pendências')" class="mb-2" />
                <div class="flex items-center" style="padding-top: 1rem;">
                    <input type="checkbox" name="vis_pendencias" id="vis_pendencias" value="1" {{ old('vis_pendencias') ? 'checked' : '' }}
                           class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_pendencias" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Houve alguma pendência na visita?') }}</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" style="padding-bottom: 1rem;">
                    {{ __('Se marcado, você pode informar as pendências no campo de observações abaixo.') }}
                </p>
            </div>

            <x-form-field name="vis_observacoes" :label="__('Observações')" :help="__('Utilize este campo para registrar observações adicionais sobre a visita, como condições encontradas, dificuldades enfrentadas ou recomendações.')">
                <textarea name="vis_observacoes" id="vis_observacoes" rows="5" class="v-input mt-1">{{ old('vis_observacoes') }}</textarea>
            </x-form-field>

            <div class="border-t border-gray-200 dark:border-gray-600 pt-6 space-y-3">
                <span id="visita-online-desc" hidden aria-hidden="true"></span>
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" id="btn-registrar-visita"
                            class="v-btn-primary px-6 disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ __('Registrar visita') }}
                    </button>
                    <span id="visita-online-status" class="text-sm text-gray-500 dark:text-gray-400"></span>
                </div>
            </div>
        </form>
    </x-section-card>
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
                    erroEl.textContent = window.__visitaFormStrings.selectLocalBeforeSuggestions;
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
                fetch(fullUrl, { credentials: 'same-origin', headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
                    .then(function(r) {
                        return r.text().then(function(text) {
                            if (!r.ok) {
                                var V = window.__visitaFormStrings;
                                var msg = (V.errorWithStatus || '').replace(':status', String(r.status));
                                if (r.status === 401 || r.status === 419) msg = V.sessionExpired;
                                else if (text && text.trim().startsWith('{')) try { var d = JSON.parse(text); if (d.message) msg = d.message; } catch(e) {}
                                throw new Error(msg);
                            }
                            if (!text || typeof text !== 'string') throw new Error(window.__visitaFormStrings.invalidResponse);
                            var trim = text.trim();
                            if (trim.charAt(0) !== '{') throw new Error(trim.indexOf('<!') !== -1 ? window.__visitaFormStrings.couldNotLoadSuggestions : window.__visitaFormStrings.invalidResponse);
                            try { return JSON.parse(text); } catch (e) { throw new Error(window.__visitaFormStrings.invalidResponse); }
                        });
                    })
                    .then(function(data) {
                        var sugestoes = (data && data.sugestoes) ? data.sugestoes : [];
                        loading.style.display = 'none';
                        if (sugestoes.length === 0) {
                            emptyEl.style.display = '';
                            return;
                        }
                        buttonsWrap.innerHTML = '';
                        sugestoes.forEach(function(s, idx) {
                            var bt = document.createElement('button');
                            bt.type = 'button';
                            bt.className = 'v-chip-suggestion v-chip-suggestion--' + (idx % 5);
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
                        erroEl.textContent = e.message || window.__visitaFormStrings.loadFailed;
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
            var V = window.__visitaFormStrings;
            var online = navigator.onLine;
            var base = ' px-6 disabled:opacity-50 disabled:cursor-not-allowed';
            if (online) {
                btn.textContent = V.registerVisit;
                btn.className = 'v-btn-primary' + base;
                desc.textContent = V.registeredNow;
                statusEl.textContent = V.withInternet;
            } else {
                btn.textContent = V.saveVisit;
                btn.className = 'v-btn-amber-solid' + base;
                desc.textContent = V.savedDevice;
                statusEl.textContent = V.withoutInternet;
            }
        }

        function saveDraft() {
            var form = document.querySelector(`form[action="{{ route('agente.visitas.store') }}"]`);
            if (!form) return Promise.reject();
            var localIdEl = form.querySelector('input[name="fk_local_id"]');
            var localDraftIdEl = form.querySelector('input[name="local_draft_id"]');
            var localIdVal = localIdEl ? parseInt(localIdEl.value, 10) : 0;
            var localDraftIdVal = localDraftIdEl ? (localDraftIdEl.value || '').trim() : '';
            if (!localIdVal && !localDraftIdVal) {
                alert(window.__visitaFormStrings.selectLocalBeforeSave);
                return Promise.reject();
            }
            var moradorObs = {};
            form.querySelectorAll('textarea[name^="morador_obs["]').forEach(function(el) {
                var m = el.name.match(/^morador_obs\[(\d+)\]$/);
                if (m) moradorObs[m[1]] = el.value || '';
            });
            var payload = {
                vis_data: (form.querySelector('input[name="vis_data"]') || {}).value || '',
                vis_ciclo: (form.querySelector('input[name="vis_ciclo"]') || {}).value || '',
                fk_local_id: localIdVal || null,
                vis_atividade: (form.querySelector('select[name="vis_atividade"]') || {}).value || '',
                vis_visita_tipo: (form.querySelector('select[name="vis_visita_tipo"]') || {}).value || '',
                vis_observacoes: (form.querySelector('textarea[name="vis_observacoes"]') || {}).value || '',
                morador_obs: moradorObs,
                vis_pendencias: (form.querySelector('input[name="vis_pendencias"]') || {}).checked ? 1 : 0,
                vis_coleta_amostra: (form.querySelector('input[name="vis_coleta_amostra"]') || {}).checked ? 1 : 0,
                vis_amos_inicial: parseInt((form.querySelector('input[name="vis_amos_inicial"]') || {}).value || 0, 10) || null,
                vis_amos_final: parseInt((form.querySelector('input[name="vis_amos_final"]') || {}).value || 0, 10) || null,
                vis_qtd_tubitos: parseInt((form.querySelector('input[name="vis_qtd_tubitos"]') || {}).value || 0, 10) || null,
                vis_depositos_eliminados: parseInt((form.querySelector('input[name="vis_depositos_eliminados"]') || {}).value || 0, 10) || null
            };
            if (localDraftIdVal) payload.local_draft_id = localDraftIdVal;
            ['insp_a1','insp_a2','insp_b','insp_c','insp_d1','insp_d2','insp_e'].forEach(function(name) {
                var el = form.querySelector('input[name="' + name + '"]');
                payload[name] = el ? (parseInt(el.value, 10) || null) : null;
            });
            payload.doencas = [];
            form.querySelectorAll('input[name="doencas[]"]:checked').forEach(function(cb) { payload.doencas.push(parseInt(cb.value, 10)); });
            var tratInput = form.querySelector('input[name="tratamentos"]');
            payload.tratamentos = (tratInput && tratInput.value) ? (function(){ try { return JSON.parse(tratInput.value); } catch (e) { return []; } })() : [];
            if (typeof window.VisitaOfflineSaveDraft !== 'function') {
                alert(window.__visitaFormStrings.offlineUnavailable);
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
                document.querySelector(`form[action="{{ route('agente.visitas.store') }}"]`).submit();
            } else {
                btn.disabled = true;
                saveDraft().then(function() {
                    if (window.VisitaOfflineUpdateBanner) window.VisitaOfflineUpdateBanner();
                    var indexUrl = <?php echo json_encode(route('agente.visitas.index')); ?>;
                    setTimeout(function() { window.location.replace(indexUrl + '?guardada=1'); }, 100);
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
            var form = document.querySelector(`form[action="{{ route('agente.visitas.store') }}"]`);
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
            setTimeout(function() {
                if (!p.morador_obs || typeof p.morador_obs !== 'object') return;
                Object.keys(p.morador_obs).forEach(function(id) {
                    var el = form.querySelector('textarea[name="morador_obs[' + id + ']"]');
                    if (el) el.value = p.morador_obs[id] != null ? String(p.morador_obs[id]) : '';
                });
            }, 250);
        }).catch(function() {});
        }
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', function() { setTimeout(apply, 80); });
        else setTimeout(apply, 80);
    })();
</script>
@endsection