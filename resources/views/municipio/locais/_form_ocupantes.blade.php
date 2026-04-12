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
        resumo: {
            totalMoradores: 0,
            totalContribuintes: 0,
            principalFonteRenda: '{{ __('Não informado') }}',
            sexo: [],
            escolaridade: [],
            renda: [],
            trabalho: []
        },
        rendaPeso: {
            nao_informado: 0,
            ate_meio_salario: 1,
            ate_1_sm: 2,
            ate_2_sm: 3,
            ate_3_sm: 4,
            acima_3_sm: 5,
            acima_5_sm: 6
        },
        trabalhoContribui: ['empregado', 'autonomo', 'aposentado', 'outro'],
        init() {
            this.syncSocioFromRows();
        },
        hasValue(v) {
            return !(v === null || v === undefined || (typeof v === 'string' && v.trim() === ''));
        },
        normalizeFlag(v) {
            return v === true || v === 1 || v === '1' || v === 'true';
        },
        rowFilled(row) {
            if (!row || typeof row !== 'object') return false;
            return this.hasValue(row.mor_nome)
                || this.hasValue(row.mor_data_nascimento)
                || this.hasValue(row.mor_telefone)
                || this.hasValue(row.mor_escolaridade)
                || this.hasValue(row.mor_renda_faixa)
                || this.hasValue(row.mor_situacao_trabalho)
                || this.hasValue(row.mor_renda_formal_informal)
                || this.normalizeFlag(row.mor_referencia_familiar);
        },
        groupBy(rows, key, labels) {
            var counts = {};
            rows.forEach(function(row) {
                var raw = row && row[key] != null ? String(row[key]).trim() : '';
                if (!raw || raw === 'nao_informado') return;
                counts[raw] = (counts[raw] || 0) + 1;
            });
            return Object.keys(counts)
                .sort(function(a, b) { return counts[b] - counts[a]; })
                .map(function(k) {
                    var label = (labels && labels[k]) ? labels[k] : k;
                    return label + ': ' + counts[k];
                });
        },
        pickFontePrincipal(rows) {
            var counts = {};
            rows.forEach(function(row) {
                var k = row && row.mor_situacao_trabalho ? String(row.mor_situacao_trabalho).trim() : '';
                if (!k || k === 'nao_informado') return;
                counts[k] = (counts[k] || 0) + 1;
            });
            var best = Object.keys(counts).sort(function(a, b) { return counts[b] - counts[a]; })[0] || '';
            if (!best) return '';
            return this.trabOpts[best] || best;
        },
        rendaFamiliarPorMoradores(rows) {
            var best = 'nao_informado';
            var bestW = 0;
            for (var i = 0; i < rows.length; i++) {
                var k = rows[i] && rows[i].mor_renda_faixa ? String(rows[i].mor_renda_faixa).trim() : '';
                if (!k || !Object.prototype.hasOwnProperty.call(this.rendaPeso, k)) continue;
                var w = this.rendaPeso[k] || 0;
                if (w > bestW) { best = k; bestW = w; }
            }
            return best;
        },
        rendaFormalDomicilio(rows) {
            var hasFormal = false;
            var hasInformal = false;
            rows.forEach(function(row) {
                var k = row && row.mor_renda_formal_informal ? String(row.mor_renda_formal_informal).trim() : '';
                if (k === 'formal') hasFormal = true;
                if (k === 'informal') hasInformal = true;
                if (k === 'misto') { hasFormal = true; hasInformal = true; }
            });
            if (hasFormal && hasInformal) return 'misto';
            if (hasFormal) return 'formal';
            if (hasInformal) return 'informal';
            return 'nao_informado';
        },
        writeSocioField(name, value) {
            var el = document.querySelector('[name="' + name + '"]');
            if (!el) return;
            if (el.getAttribute('data-autofill-from-ocupantes') !== '1') return;
            var normalized = value == null ? '' : String(value);
            if (el.value === normalized) return;
            el.value = normalized;
            el.dispatchEvent(new Event('input', { bubbles: true }));
            el.dispatchEvent(new Event('change', { bubbles: true }));
        },
        syncSocioFromRows() {
            var rows = this.rows.filter((row) => this.rowFilled(row));
            var totalMoradores = rows.length;
            var totalContribuintes = rows.filter((row) => {
                var renda = row && row.mor_renda_faixa ? String(row.mor_renda_faixa).trim() : '';
                if (renda && renda !== 'nao_informado') return true;
                var trab = row && row.mor_situacao_trabalho ? String(row.mor_situacao_trabalho).trim() : '';
                return this.trabalhoContribui.indexOf(trab) >= 0;
            }).length;
            var titular = rows.find((row) => this.normalizeFlag(row.mor_referencia_familiar));
            var telefone = titular && this.hasValue(titular.mor_telefone)
                ? String(titular.mor_telefone).trim()
                : ((rows.find((row) => this.hasValue(row.mor_telefone)) || {}).mor_telefone || '');
            var fontePrincipal = this.pickFontePrincipal(rows);

            this.writeSocioField('socio[n_moradores_declarado]', totalMoradores > 0 ? totalMoradores : '');
            this.writeSocioField('socio[qtd_contribuintes]', totalContribuintes > 0 ? totalContribuintes : '');
            this.writeSocioField('socio[renda_familiar_faixa]', this.rendaFamiliarPorMoradores(rows));
            this.writeSocioField('socio[renda_formal_informal]', this.rendaFormalDomicilio(rows));
            this.writeSocioField('socio[principal_fonte_renda]', fontePrincipal);
            this.writeSocioField('socio[telefone_contato]', telefone);
            this.writeSocioField('socio[posicao_entrevistado]', titular ? 'titular' : (rows.length > 0 ? 'morador' : 'nao_informado'));

            this.resumo = {
                totalMoradores: totalMoradores,
                totalContribuintes: totalContribuintes,
                principalFonteRenda: fontePrincipal || '{{ __('Não informado') }}',
                sexo: this.groupBy(rows, 'mor_sexo', this.sexoOpts),
                escolaridade: this.groupBy(rows, 'mor_escolaridade', this.esc),
                renda: this.groupBy(rows, 'mor_renda_faixa', this.rendaOpts),
                trabalho: this.groupBy(rows, 'mor_situacao_trabalho', this.trabOpts)
            };
        },
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
        <div x-effect="syncSocioFromRows()"></div>

        <div class="rounded-lg border border-emerald-200 bg-emerald-50/60 p-3 text-xs text-emerald-900 dark:border-emerald-800 dark:bg-emerald-900/20 dark:text-emerald-100">
            <p class="font-semibold">{{ __('Resumo automático por moradores (preenche a ficha socioeconômica)') }}</p>
            <p class="mt-1">{{ __('Moradores:') }} <span x-text="resumo.totalMoradores"></span> · {{ __('Contribuintes:') }} <span x-text="resumo.totalContribuintes"></span> · {{ __('Fonte principal:') }} <span x-text="resumo.principalFonteRenda"></span></p>
            <p class="mt-1" x-show="resumo.sexo.length">{{ __('Sexo:') }} <span x-text="resumo.sexo.join(' | ')"></span></p>
            <p class="mt-1" x-show="resumo.escolaridade.length">{{ __('Escolaridade:') }} <span x-text="resumo.escolaridade.join(' | ')"></span></p>
            <p class="mt-1" x-show="resumo.renda.length">{{ __('Renda:') }} <span x-text="resumo.renda.join(' | ')"></span></p>
            <p class="mt-1" x-show="resumo.trabalho.length">{{ __('Trabalho:') }} <span x-text="resumo.trabalho.join(' | ')"></span></p>
        </div>

        <template x-for="(row, idx) in rows" :key="idx">
            <div class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 space-y-3 dark:border-slate-600 dark:bg-slate-900/30">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300" x-text="row.mor_id ? '{{ __('Ocupante') }} #' + row.mor_id : '{{ __('Novo ocupante') }}'"></span>
                    <button type="button" class="text-xs text-red-600 hover:underline dark:text-red-400" @click="removeRow(idx)" x-show="!row.mor_id && rows.length > 1">{{ __('Remover linha') }}</button>
                </div>
                <input type="hidden" x-bind:name="'ocupantes[' + idx + '][mor_id]'" x-bind:value="row.mor_id != null && row.mor_id !== '' ? row.mor_id : ''">
                <fieldset class="space-y-3">
                    <legend class="v-toolbar-label">{{ __('Dados pessoais') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="v-toolbar-label">{{ __('Nome') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_nome]'" x-model="row.mor_nome" class="v-input mt-1 w-full" maxlength="255">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Data de nascimento') }}</label>
                            <input type="date" x-bind:name="'ocupantes[' + idx + '][mor_data_nascimento]'" x-model="row.mor_data_nascimento" class="v-input mt-1 w-full">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Sexo') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_sexo]'" x-model="row.mor_sexo" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(sexoOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Estado civil') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_estado_civil]'" x-model="row.mor_estado_civil" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(ecOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-toolbar-label">{{ __('Vínculo familiar e contato') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="v-toolbar-label">{{ __('Referência familiar (titular)') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_referencia_familiar]'"
                                    x-model="row.mor_referencia_familiar"
                                    @change="if (String(row.mor_referencia_familiar) !== '0') row.mor_parentesco = ''"
                                    class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <option value="1">{{ __('Sim') }}</option>
                                <option value="0">{{ __('Não') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Defina apenas uma pessoa como referência familiar.') }}</p>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Parentesco com o titular') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_parentesco]'"
                                    x-model="row.mor_parentesco"
                                    x-bind:disabled="String(row.mor_referencia_familiar) !== '0'"
                                    class="v-select mt-1 w-full disabled:cursor-not-allowed disabled:bg-slate-100 disabled:text-slate-500 dark:disabled:bg-slate-800/60 dark:disabled:text-slate-400">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(parOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Telefone') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_telefone]'" x-model="row.mor_telefone" class="v-input mt-1 w-full" maxlength="40">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Naturalidade') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_naturalidade]'" x-model="row.mor_naturalidade" class="v-input mt-1 w-full" maxlength="150" placeholder="{{ __('Cidade/UF') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-toolbar-label">{{ __('Documentos') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="v-toolbar-label">{{ __('CPF') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_cpf]'" x-model="row.mor_cpf" class="v-input mt-1 w-full" maxlength="20">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('RG: número') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_numero]'" x-model="row.mor_rg_numero" class="v-input mt-1 w-full" maxlength="45">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="v-toolbar-label">{{ __('RG: órgão') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_orgao]'" x-model="row.mor_rg_orgao" class="v-input mt-1 w-full" maxlength="60">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-toolbar-label">{{ __('Perfil socioeconômico') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <label class="v-toolbar-label">{{ __('Escolaridade') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_escolaridade]'" x-model="row.mor_escolaridade" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(esc)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Cor ou raça') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_cor_raca]'" x-model="row.mor_cor_raca" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(corOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Profissão / ocupação declarada') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_profissao]'" x-model="row.mor_profissao" class="v-input mt-1 w-full" maxlength="150">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Situação no trabalho') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_situacao_trabalho]'" x-model="row.mor_situacao_trabalho" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(trabOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Faixa de renda (salário mínimo)') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_renda_faixa]'" x-model="row.mor_renda_faixa" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(rendaOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Renda formal / informal (pessoa)') }}</label>
                            <select x-bind:name="'ocupantes[' + idx + '][mor_renda_formal_informal]'" x-model="row.mor_renda_formal_informal" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(rfiOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Tempo com cônjuge / união') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_tempo_uniao_conjuge]'" x-model="row.mor_tempo_uniao_conjuge" class="v-input mt-1 w-full" maxlength="120">
                        </div>
                        <div>
                            <label class="v-toolbar-label">{{ __('Ajudou na compra/construção do imóvel') }}</label>
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_ajuda_compra_imovel]'" x-model="row.mor_ajuda_compra_imovel" class="v-input mt-1 w-full" maxlength="255">
                        </div>
                    </div>
                </fieldset>

                <div>
                    <label class="v-toolbar-label">{{ __('Observações') }}</label>
                    <textarea x-bind:name="'ocupantes[' + idx + '][mor_observacao]'" x-model="row.mor_observacao" rows="2" class="v-input mt-1 w-full"></textarea>
                </div>
            </div>
        </template>
        <button type="button" @click="addRow()" class="v-btn-compact v-btn-compact--blue text-sm">
            + {{ __('Adicionar morador') }}
        </button>
    </div>
</fieldset>
