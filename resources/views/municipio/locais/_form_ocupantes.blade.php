{{-- Cadastro de ocupantes junto ao imóvel (opcional). $local opcional na edição. --}}
@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $ocupantesRows = old('ocupantes');
    if (! is_array($ocupantesRows) && isset($local)) {
        $local->loadMissing('moradores');
        $ocupantesRows = $local->moradores->map(function ($m) {
            return [
                'mor_id' => $m->mor_id,
                'mor_nome' => $m->mor_nome,
                'mor_data_nascimento' => $m->mor_data_nascimento ? $m->mor_data_nascimento->format('Y-m-d') : '',
                'mor_escolaridade' => $m->mor_escolaridade ?? '',
                'mor_renda_faixa' => $m->mor_renda_faixa ?? '',
                'mor_cor_raca' => $m->mor_cor_raca ?? '',
                'mor_situacao_trabalho' => $m->mor_situacao_trabalho ?? '',
                'mor_observacao' => $m->mor_observacao ?? '',
            ];
        })->values()->all();
    }
    if (! is_array($ocupantesRows) || count($ocupantesRows) === 0) {
        $ocupantesRows = [[
            'mor_id' => null,
            'mor_nome' => '',
            'mor_data_nascimento' => '',
            'mor_escolaridade' => '',
            'mor_renda_faixa' => '',
            'mor_cor_raca' => '',
            'mor_situacao_trabalho' => '',
            'mor_observacao' => '',
        ]];
    }
    $disclaimer = config('visitaai_municipio.ocupantes.disclaimer', '');
    $emptyRow = [
        'mor_id' => null,
        'mor_nome' => '',
        'mor_data_nascimento' => '',
        'mor_escolaridade' => '',
        'mor_renda_faixa' => '',
        'mor_cor_raca' => '',
        'mor_situacao_trabalho' => '',
        'mor_observacao' => '',
    ];
@endphp

<x-lgpd.aviso context="ocupantes_cadastro" class="mb-4" />
<fieldset class="space-y-3 border-t border-gray-200 pt-6 mt-2 dark:border-gray-600">
    <legend class="v-section-title mb-2">{{ __('Ocupantes do imóvel (Visita Aí)') }}</legend>
    @if(filled($disclaimer))
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $disclaimer }}</p>
    @endif
    <div x-data="{
        rows: {{ Js::from($ocupantesRows) }},
        esc: {{ Js::from($esc) }},
        rendaOpts: {{ Js::from($renda) }},
        corOpts: {{ Js::from($cor) }},
        trabOpts: {{ Js::from($trab) }},
        emptyRow: {{ Js::from($emptyRow) }},
        addRow() {
            this.rows.push({ ...this.emptyRow });
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
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nome (opcional)') }}</label>
                        <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_nome]'" x-model="row.mor_nome" class="v-input mt-1 w-full" maxlength="255">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Data de nascimento') }}</label>
                        <input type="date" x-bind:name="'ocupantes[' + idx + '][mor_data_nascimento]'" x-model="row.mor_data_nascimento" class="v-input mt-1 w-full">
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
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Faixa de renda (referência salário mínimo)') }}</label>
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
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Cor ou raça (autodeclarada)') }}</label>
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
            + {{ __('Adicionar ocupante') }}
        </button>
    </div>
</fieldset>
