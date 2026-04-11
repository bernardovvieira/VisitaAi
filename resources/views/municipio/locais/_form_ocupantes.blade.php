{{-- Cadastro de ocupantes + ficha socioeconômica por pessoa. $local opcional na edição. --}}
@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $sexo = config('visitaai_socioeconomico.sexo_opcoes', []);
    $ec = config('visitaai_socioeconomico.estado_civil_opcoes', []);
    $par = config('visitaai_socioeconomico.parentesco_opcoes', []);
    $rfi = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
    $mapMorador = function ($m) {
        return [
            'mor_id' => $m->mor_id,
            'mor_nome' => $m->mor_nome,
            'mor_data_nascimento' => $m->mor_data_nascimento ? $m->mor_data_nascimento->format('Y-m-d') : '',
            'mor_escolaridade' => $m->mor_escolaridade ?? '',
            'mor_renda_faixa' => $m->mor_renda_faixa ?? '',
            'mor_cor_raca' => $m->mor_cor_raca ?? '',
            'mor_situacao_trabalho' => $m->mor_situacao_trabalho ?? '',
            'mor_sexo' => $m->mor_sexo ?? '',
            'mor_estado_civil' => $m->mor_estado_civil ?? '',
            'mor_naturalidade' => $m->mor_naturalidade ?? '',
            'mor_profissao' => $m->mor_profissao ?? '',
            'mor_parentesco' => $m->mor_parentesco ?? '',
            'mor_referencia_familiar' => (bool) ($m->mor_referencia_familiar ?? false),
            'mor_telefone' => $m->mor_telefone ?? '',
            'mor_rg_numero' => $m->mor_rg_numero ?? '',
            'mor_rg_orgao' => $m->mor_rg_orgao ?? '',
            'mor_cpf' => $m->mor_cpf ?? '',
            'mor_tempo_uniao_conjuge' => $m->mor_tempo_uniao_conjuge ?? '',
            'mor_ajuda_compra_imovel' => $m->mor_ajuda_compra_imovel ?? '',
            'mor_renda_formal_informal' => $m->mor_renda_formal_informal ?? '',
            'mor_observacao' => $m->mor_observacao ?? '',
        ];
    };
    $ocupantesRows = old('ocupantes');
    if (! is_array($ocupantesRows) && isset($local)) {
        $local->loadMissing('moradores');
        $ocupantesRows = $local->moradores->map($mapMorador)->values()->all();
    }
    $emptyRow = [
        'mor_id' => null,
        'mor_nome' => '',
        'mor_data_nascimento' => '',
        'mor_escolaridade' => '',
        'mor_renda_faixa' => '',
        'mor_cor_raca' => '',
        'mor_situacao_trabalho' => '',
        'mor_sexo' => '',
        'mor_estado_civil' => '',
        'mor_naturalidade' => '',
        'mor_profissao' => '',
        'mor_parentesco' => '',
        'mor_referencia_familiar' => false,
        'mor_telefone' => '',
        'mor_rg_numero' => '',
        'mor_rg_orgao' => '',
        'mor_cpf' => '',
        'mor_tempo_uniao_conjuge' => '',
        'mor_ajuda_compra_imovel' => '',
        'mor_renda_formal_informal' => '',
        'mor_observacao' => '',
    ];
    if (! is_array($ocupantesRows) || count($ocupantesRows) === 0) {
        $ocupantesRows = [$emptyRow];
    }
@endphp

<fieldset class="space-y-3 border-t border-gray-200 pt-6 mt-2 dark:border-gray-600">
    <legend class="v-section-title mb-2">{{ __('Composição familiar: cadastro por morador') }}</legend>
    <p class="text-sm text-slate-600 dark:text-slate-400">{{ __('Cada pessoa pode ter qualificação completa (ficha socioeconômica). Marque no máximo um como referência familiar (titular).') }}</p>
    <div x-data="{
        rows: {{ Js::from($ocupantesRows) }},
        esc: {{ Js::from($esc) }},
        rendaOpts: {{ Js::from($renda) }},
        corOpts: {{ Js::from($cor) }},
        trabOpts: {{ Js::from($trab) }},
        sexoOpts: {{ Js::from($sexo) }},
        ecOpts: {{ Js::from($ec) }},
        parOpts: {{ Js::from($par) }},
        rfiOpts: {{ Js::from($rfi) }},
        emptyRow: {{ Js::from($emptyRow) }},
        addRow() {
            this.rows.push(JSON.parse(JSON.stringify(this.emptyRow)));
        },
        removeRow(i) {
            if (this.rows[i] && !this.rows[i].mor_id) {
                this.rows.splice(i, 1);
                if (this.rows.length === 0) this.addRow();
            }
        }
    }" class="space-y-4">
        <template x-for="(row, idx) in rows" :key="idx">
            <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 space-y-3 dark:border-slate-600 dark:bg-slate-900/30">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300" x-text="row.mor_id ? '{{ __('Ocupante') }} #' + row.mor_id : '{{ __('Novo ocupante') }}'"></span>
                    <button type="button" class="text-xs text-red-600 hover:underline dark:text-red-400" @click="removeRow(idx)" x-show="!row.mor_id && rows.length > 1">{{ __('Remover linha') }}</button>
                </div>
                <input type="hidden" x-bind:name="'ocupantes[' + idx + '][mor_id]'" x-bind:value="row.mor_id != null && row.mor_id !== '' ? row.mor_id : ''">
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nome') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_nome]'" x-model="row.mor_nome" class="v-input mt-1 w-full" maxlength="255">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Data de nascimento') }}</label>
                        <input type="date" x-bind:name="'ocupantes[' + idx + '][mor_data_nascimento]'" x-model="row.mor_data_nascimento" class="v-input mt-1 w-full">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Sexo') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_sexo]'" x-model="row.mor_sexo" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(sexoOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Estado civil') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_estado_civil]'" x-model="row.mor_estado_civil" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(ecOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Parentesco com o titular') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_parentesco]'" x-model="row.mor_parentesco" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(parOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="flex items-start gap-2 rounded border border-amber-200/80 bg-amber-50/50 px-3 py-2 dark:border-amber-900/50 dark:bg-amber-950/20">
                    <input type="checkbox" class="mt-1 rounded border-slate-300" value="1"
                        x-bind:name="'ocupantes[' + idx + '][mor_referencia_familiar]'"
                        x-bind:checked="row.mor_referencia_familiar === true || row.mor_referencia_familiar === 1 || row.mor_referencia_familiar === '1'"
                        @change="row.mor_referencia_familiar = $event.target.checked">
                    <span class="text-xs text-slate-700 dark:text-slate-300">{{ __('Referência familiar (titular da ficha): marque no máximo uma pessoa') }}</span>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Naturalidade') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_naturalidade]'" x-model="row.mor_naturalidade" class="v-input mt-1 w-full" maxlength="150" placeholder="{{ __('Cidade/UF') }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Profissão / ocupação declarada') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_profissao]'" x-model="row.mor_profissao" class="v-input mt-1 w-full" maxlength="150">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Telefone') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_telefone]'" x-model="row.mor_telefone" class="v-input mt-1 w-full" maxlength="40">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Renda formal / informal (pessoa)') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_renda_formal_informal]'" x-model="row.mor_renda_formal_informal" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(rfiOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <x-ui.disclosure variant="muted-card-simple" :open="false">
                    <x-slot name="summary">
                        <span class="border-b border-dotted border-slate-400 pb-px text-xs dark:border-slate-500">{{ __('Documentos (sensível)') }}</span>
                    </x-slot>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 pt-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('RG: número') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_numero]'" x-model="row.mor_rg_numero" class="v-input mt-1 w-full" maxlength="45">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('RG: órgão') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_orgao]'" x-model="row.mor_rg_orgao" class="v-input mt-1 w-full" maxlength="60">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('CPF') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_cpf]'" x-model="row.mor_cpf" class="v-input mt-1 w-full" maxlength="20">
                        </div>
                    </div>
                </x-ui.disclosure>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tempo com cônjuge / união') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_tempo_uniao_conjuge]'" x-model="row.mor_tempo_uniao_conjuge" class="v-input mt-1 w-full" maxlength="120">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Ajudou na compra/construção do imóvel') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_ajuda_compra_imovel]'" x-model="row.mor_ajuda_compra_imovel" class="v-input mt-1 w-full" maxlength="255">
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Escolaridade') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_escolaridade]'" x-model="row.mor_escolaridade" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(esc)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Faixa de renda (salário mínimo)') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_renda_faixa]'" x-model="row.mor_renda_faixa" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(rendaOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Cor ou raça') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_cor_raca]'" x-model="row.mor_cor_raca" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(corOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Situação no trabalho') }}</label>
                        <select x-bind:name="'ocupantes[' + idx + '][mor_situacao_trabalho]'" x-model="row.mor_situacao_trabalho" class="v-select mt-1 w-full">
                            <option value="">{{ __('Selecionar') }}</option>
                            <template x-for="[k, label] in Object.entries(trabOpts)" :key="k">
                                <option :value="k" x-text="label"></option>
                            </template>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Observações') }}</label>
                    <textarea x-bind:name="'ocupantes[' + idx + '][mor_observacao]'" x-model="row.mor_observacao" rows="2" class="v-input mt-1 w-full"></textarea>
                </div>
            </div>
        </template>
        <button type="button" @click="addRow()" class="v-btn-compact v-btn-compact--blue text-sm">
            + {{ __('Adicionar morador') }}
        </button>
    </div>
</fieldset>
