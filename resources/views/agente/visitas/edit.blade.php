@extends('layouts.app')

@section('og_title', config('app.name') . ' · ' . __('Editar visita'))
@section('og_description', __('Atualize dados da visita registrada: local, data, tratamentos e doenças monitoradas.'))

@section('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@endsection

@section('content')
<div class="v-page space-y-5">
    @include('visitas.partials._form-js-strings')
    <x-breadcrumbs :items="[['label' => __('Página Inicial'), 'url' => route('dashboard')], ['label' => __('Visitas'), 'url' => route('agente.visitas.index')], ['label' => __('Editar')]]" />

    <x-page-header :eyebrow="__('Registro em campo')" :title="__('Editar visita')">
        <x-slot name="lead">
            <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Atualize local, data, tratamentos e doenças monitoradas.') }}</p>
        </x-slot>
    </x-page-header>

    <x-section-card class="space-y-4 rounded-2xl border border-slate-200/80 bg-white/95 p-4 shadow-sm dark:border-slate-700/70 dark:bg-slate-900/50">
        <div class="flex items-center gap-3">
            <x-heroicon-o-wifi class="h-5 w-5 text-sky-500 dark:text-sky-400" aria-hidden="true" />
            <h3 class="text-sm font-semibold">{{ __('Modo offline e envio depois') }}</h3>
        </div>
        <p class="text-sm text-slate-700 dark:text-slate-300"><strong>{{ __('Sem internet?') }}</strong>
            {{ __('Use o botão :btn no final do formulário. A visita fica salva no seu aparelho e pode ser enviada depois em :menu.', [
                'btn' => __('Guardar no dispositivo para enviar depois'),
                'menu' => __('Enviar visitas salvas no dispositivo'),
            ]) }}</p>
        <p class="mt-2 text-sm text-slate-700 dark:text-slate-300"><span class="font-semibold">{{ __('Antes de ir a campo:') }}</span>
            {{ __('Abra a tela de registrar visita pelo menos uma vez com internet para ativar o funcionamento offline no dispositivo.') }}</p>
    </x-section-card>

    <x-section-card class="space-y-6 dark:bg-gray-800">
        @if ($errors->any())
            <x-alert type="error" :title="__('Corrija os erros nos campos indicados abaixo.')" :message="implode(' ', $errors->all())" />
        @endif

        <form method="POST" action="{{ route('agente.visitas.update', $visita) }}" class="space-y-6">
            @csrf
            @method('PUT')

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Dados básicos') }}</legend>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field name="vis_data" :label="__('Data da visita')" :required="true">
                        <x-text-input type="date" name="vis_data" id="vis_data" value="{{ old('vis_data', optional($visita)->vis_data) }}" required />
                    </x-form-field>
                    <x-form-field name="vis_ciclo" :label="__('Ciclo/ano')" :required="true">
                        <x-text-input type="text" name="vis_ciclo" id="vis_ciclo" value="{{ old('vis_ciclo', optional($visita)->vis_ciclo) }}" required placeholder="{{ __('mm/aa') }}" />
                    </x-form-field>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('O ciclo deve ser informado no formato mm/aa, por exemplo, :exemplo para a primeira referência do ano de :ano.', ['exemplo' => '"01/'.now()->format('y').'"', 'ano' => (string) now()->year]) }}
                </p>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Localização') }}</legend>
                <div class="col-span-1 sm:col-span-2"
                    x-data="{
                        open: false,
                        search: '{{ old('fk_local_id') ? '' : ($visita?->local->loc_codigo_unico . ' - ' . $visita?->local->loc_endereco) }}',
                        selectedId: '{{ old('fk_local_id', $visita->fk_local_id ?? '') }}',
                        locais: {{ Js::from($locais) }},
                        oldMoradorObs: @js((object) old('morador_obs', $visita->vis_ocupantes_observacoes ?? [])),
                        limparSelecao() {
                            if (!this.selectedId) return;
                            this.selectedId = '';
                            this.search = '';
                            this.open = true;
                            this.$nextTick(() => this.$refs.input.focus());
                        }
                    }"
                    x-init="$watch('search', () => open = true)">

                    <x-input-label for="fk_local_id" :value="__('Local visitado')" class="mb-2" />
                    <div class="relative mt-1">
                        <input type="text" x-model="search" @click="limparSelecao" x-ref="input" placeholder="{{ __('Buscar local...') }}" required
                            class="v-input">

                        <ul x-show="open" @click.away="open = false" x-cloak class="absolute z-10 mt-1 w-full bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md shadow max-h-60 overflow-auto"
                            x-transition>
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
                                    <button type="button"
                                            @click="selectedId = local.loc_id; search = window.__visitaFormatLocalLine(local); open = false"
                                            class="v-list-item-hover block w-full px-3 py-2 text-left text-sm text-gray-700 dark:text-gray-100">
                                        <span x-text="window.__visitaFormatLocalLine(local)"></span>
                                    </button>
                                </li>
                            </template>
                        </ul>
                    </div>
                    <input type="hidden" name="fk_local_id" :value="selectedId">

                    <div class="mt-4 space-y-3 border-t border-gray-200 pt-4 dark:border-gray-600" x-show="selectedId" x-cloak>
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
                        <p class="text-xs text-gray-500 dark:text-gray-400" x-show="selectedId && (!((locais.find(l => Number(l.loc_id) === Number(selectedId)) || {}).moradores) || !((locais.find(l => Number(l.loc_id) === Number(selectedId)) || {}).moradores).length)">
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
                    <select id="vis_atividade" name="vis_atividade" required class="v-select mt-1">
                        <option value="">{{ __('Selecione…') }}</option>
                        @foreach(config('ms_terminologia.atividades_pncd') as $cod => $at)
                            <option value="{{ $cod }}" {{ old('vis_atividade', $visita->vis_atividade ?? '') == $cod ? 'selected' : '' }}>{{ $at['codigo'] }} · {{ __($at['nome']) }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Tipo da visita') }}</legend>
                <x-form-field name="vis_visita_tipo" :label="__('Tipo da visita')">
                    <select name="vis_visita_tipo" id="vis_visita_tipo" class="v-select mt-1">
                        <option value="">{{ __('Selecione…') }}</option>
                        @foreach(config('ms_terminologia.visita_tipo') as $tipoVal => $tipoConf)
                            <option value="{{ $tipoVal }}" {{ old('vis_visita_tipo', $visita->vis_visita_tipo ?? '') == $tipoVal ? 'selected' : '' }}>{{ __($tipoConf['label']) }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Depósitos inspecionados') }}</legend>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach (['a1', 'a2', 'b', 'c', 'd1', 'd2', 'e'] as $tipo)
                        <div>
                            <label for="insp_{{ $tipo }}" class="block text-xs font-medium text-gray-600 dark:text-gray-300 uppercase">{{ __('Depósito :tipo', ['tipo' => strtoupper($tipo)]) }}</label>
                            <input id="insp_{{ $tipo }}" name="insp_{{ $tipo }}" type="number" min="0" value="{{ old('insp_' . $tipo, optional($visita)->{'insp_' . $tipo}) }}"
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
                    <x-text-input type="number" name="vis_depositos_eliminados" id="vis_depositos_eliminados" min="0" value="{{ old('vis_depositos_eliminados', optional($visita)->vis_depositos_eliminados) }}" />
                </x-form-field>
            </fieldset>

            <fieldset class="space-y-3">
                <legend class="v-section-title mb-2">{{ __('Coleta de amostra') }}</legend>
                <div class="flex items-center mt-6">
                    <input type="checkbox" name="vis_coleta_amostra" id="vis_coleta_amostra" value="1" {{ old('vis_coleta_amostra', optional($visita)->vis_coleta_amostra) ? 'checked' : '' }}
                        class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_coleta_amostra" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Houve coleta de amostra?') }}</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Se marcado, informe as amostras e a quantidade de tubitos usados.') }}
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form-field name="vis_amos_inicial" :label="__('Número de amostra inicial')">
                        <x-text-input type="number" name="vis_amos_inicial" id="vis_amos_inicial" min="0" value="{{ old('vis_amos_inicial', optional($visita)->vis_amos_inicial) }}" />
                    </x-form-field>
                    <x-form-field name="vis_amos_final" :label="__('Número de amostra final')">
                        <x-text-input type="number" name="vis_amos_final" id="vis_amos_final" min="0" value="{{ old('vis_amos_final', optional($visita)->vis_amos_final) }}" />
                    </x-form-field>
                </div>
                <x-form-field name="vis_qtd_tubitos" :label="__('Quantidade de tubitos utilizados')">
                    <x-text-input type="number" name="vis_qtd_tubitos" id="vis_qtd_tubitos" min="0" value="{{ old('vis_qtd_tubitos', optional($visita)->vis_qtd_tubitos) }}" />
                </x-form-field>
            </fieldset>

            {{-- Tratamentos --}}
            <div x-data="{ exibirTratamentos: {{ old('tratamentos') || ($visita->tratamentos && count($visita->tratamentos)) ? 'true' : 'false' }}, tratamentos: {{ old('tratamentos', json_encode($visita->tratamentos ?? [])) }} }" class="space-y-4">
                <x-input-label :value="__('Tratamentos')" class="mb-2" />

                <template x-if="tratamentos.length === 0">
                    <div class="p-4 bg-gray-50 dark:bg-gray-800 text-sm text-gray-700 dark:text-gray-300 rounded">
                        {{ __('Nenhum tratamento foi informado. Adicione se necessário.') }}
                        <div class="mt-2">
                            <button type="button"
                                    @click="exibirTratamentos = true; tratamentos.push({trat_forma:'Focal', linha:'1', trat_tipo:'Larvicida', qtd_gramas:null, qtd_depositos_tratados:null, qtd_cargas:null})"
                                    class="v-btn-compact v-btn-compact--blue">
                                {{ __('+ Adicionar tratamento') }}
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
                                            <option value="Focal">{{ __('Focal') }}</option>
                                            <option value="Perifocal">{{ __('Perifocal') }}</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tipo') }}</label>
                                        <input type="text" :name="`tratamentos[${i}][trat_tipo]`" x-model="t.trat_tipo" readonly
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
            {{-- Sugestões de doenças (JS puro) --}}
            <div id="sugestoes-doencas-wrap" class="space-y-2" data-sugestoes-url="{{ route('agente.sugestoes-doencas') }}" data-local-id="{{ $visita->fk_local_id }}">
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

            {{-- Doenças Monitoradas --}}
            <fieldset class="space-y-3">
                @php
                    $vDoencaTones = [
                        'rounded-xl border border-amber-200/90 bg-amber-50/50 p-2.5 dark:border-amber-800/45 dark:bg-amber-950/20',
                        'rounded-xl border border-sky-200/90 bg-sky-50/50 p-2.5 dark:border-sky-800/45 dark:bg-sky-950/20',
                        'rounded-xl border border-violet-200/90 bg-violet-50/50 p-2.5 dark:border-violet-800/45 dark:bg-violet-950/20',
                        'rounded-xl border border-emerald-200/90 bg-emerald-50/50 p-2.5 dark:border-emerald-800/45 dark:bg-emerald-950/20',
                        'rounded-xl border border-rose-200/90 bg-rose-50/50 p-2.5 dark:border-rose-800/45 dark:bg-rose-950/20',
                    ];
                @endphp
                <legend class="v-section-title mb-2">{{ __('Possíveis doenças') }}</legend>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-2">
                    @foreach ($doencas as $doenca)
                        <div class="{{ $vDoencaTones[$loop->index % 5] }} flex min-h-[2.75rem] items-center gap-2.5">
                            <input type="checkbox"
                                id="doenca_{{ $doenca->doe_id }}"
                                name="doencas[]"
                                value="{{ $doenca->doe_id }}"
                                {{ in_array($doenca->doe_id, old('doencas', $visita->doencas->pluck('doe_id')->toArray())) ? 'checked' : '' }}
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

            <div class="space-y-3">
                <x-input-label :value="__('Pendências')" class="mb-2" />
                <div class="flex items-center pt-4">
                    <input type="checkbox" name="vis_pendencias" id="vis_pendencias" value="1" {{ old('vis_pendencias', optional($visita)->vis_pendencias) ? 'checked' : '' }}
                        class="mr-2 text-blue-600 dark:text-blue-400">
                    <label for="vis_pendencias" class="text-sm text-gray-700 dark:text-gray-300">{{ __('Houve alguma pendência na visita?') }}</label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 pb-4">
                    {{ __('Se marcado, você pode informar as pendências no campo de observações abaixo.') }}
                </p>
            </div>

            <x-form-field name="vis_observacoes" :label="__('Observações')" :help="__('Utilize este campo para registrar observações adicionais sobre a visita, como condições encontradas, dificuldades enfrentadas ou recomendações.')">
                <textarea name="vis_observacoes" id="vis_observacoes" rows="5" class="v-input mt-1">{{ old('vis_observacoes', optional($visita)->vis_observacoes) }}</textarea>
            </x-form-field>

            <div class="flex justify-end">
                <button type="submit"
                        class="v-btn-primary px-6">
                    {{ __('Atualizar visita') }}
                </button>
            </div>
        </form>
    </x-section-card>
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
                if (!r.ok) throw new Error((window.__visitaFormStrings.errorWithStatus || '').replace(':status', String(r.status)));
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
                erroEl.textContent = e.message || window.__visitaFormStrings.fetchDiseaseSuggestionsFailed;
                erroEl.style.display = '';
            });
    });
});
</script>
@endsection