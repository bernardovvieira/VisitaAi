{{-- Cadastro de ocupantes + ficha socioeconômica por pessoa. $local opcional na edição. --}}
@php
    $local = $local ?? null;
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $sexo = config('visitaai_socioeconomico.sexo_opcoes', []);
    $ec = config('visitaai_socioeconomico.estado_civil_opcoes', []);
    $par = config('visitaai_socioeconomico.parentesco_opcoes', []);
    $rfi = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
    $rp = auth()->user()->locaisRouteProfile();
    $mapMorador = function ($m) use ($local, $rp) {
        $docs = [];
        if ($m->relationLoaded('documentosPessoais')) {
            foreach ($m->documentosPessoais as $d) {
                $docs[] = [
                    'id' => $d->id,
                    'nome' => $d->original_name,
                    'download_url' => (isset($local) && $local->exists && $m->mor_id)
                        ? route($rp.'.locais.moradores.documento-pessoal', [$local, $m, $d])
                        : '',
                ];
            }
        }

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
            'mor_rg_expedicao' => $m->mor_rg_expedicao ? $m->mor_rg_expedicao->format('Y-m-d') : '',
            'mor_cpf' => $m->mor_cpf ?? '',
            'documentos_pessoais' => $docs,
            'mor_tempo_uniao_conjuge' => $m->mor_tempo_uniao_conjuge ?? '',
            'mor_ajuda_compra_imovel' => in_array(strtolower(trim((string) ($m->mor_ajuda_compra_imovel ?? ''))), ['sim', 'nao'], true)
                ? strtolower(trim((string) ($m->mor_ajuda_compra_imovel ?? '')))
                : '',
            'mor_renda_formal_informal' => $m->mor_renda_formal_informal ?? '',
            'mor_observacao' => $m->mor_observacao ?? '',
        ];
    };
    $ocupantesRows = old('ocupantes');
    if (! is_array($ocupantesRows) && isset($local)) {
        $local->loadMissing('moradores.documentosPessoais');
        $ocupantesRows = $local->moradores->map($mapMorador)->values()->all();
    } elseif (is_array($ocupantesRows) && isset($local)) {
        $local->loadMissing('moradores.documentosPessoais');
        $byId = $local->moradores->keyBy('mor_id');
        foreach ($ocupantesRows as $i => $row) {
            if (! is_array($row)) {
                continue;
            }
            $mid = isset($row['mor_id']) ? (int) $row['mor_id'] : 0;
            if ($mid > 0 && $byId->has($mid)) {
                $ocupantesRows[$i] = array_merge($mapMorador($byId->get($mid)), $row);
            }
        }
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
        'mor_rg_expedicao' => '',
        'mor_cpf' => '',
        'documentos_pessoais' => [],
        'mor_tempo_uniao_conjuge' => '',
        'mor_ajuda_compra_imovel' => '',
        'mor_renda_formal_informal' => '',
        'mor_observacao' => '',
    ];
    if (! is_array($ocupantesRows) || count($ocupantesRows) === 0) {
        $ocupantesRows = [$emptyRow];
    }
@endphp

@include('municipio.locais._script_disclosure_accordion_once')

<x-ui.disclosure variant="muted-card-simple" :open="false" accordionGroup="ficha-socio">
    <style>
        .ocupante-chevron { display: inline-block; transform-origin: center; transition: transform .15s ease-in-out; margin-right: .5rem; }
    </style>
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ __('4. Composição familiar e ocupantes') }}</span>
    </x-slot>
    <div class="space-y-3">
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
        openOcupanteIdx: null,
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
                || this.hasValue(row.mor_rg_expedicao)
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
            var el = (document.getElementsByName(name) || [])[0] || null;
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
            this.openOcupanteIdx = this.rows.length - 1;
        },
        enforceSingleTitular(idx) {
            var selected = this.rows[idx] || null;
            var isTitular = selected && String(selected.mor_referencia_familiar) === '1';
            if (!isTitular) return;
            this.rows.forEach(function(r, i) {
                if (i === idx) return;
                if (String(r.mor_referencia_familiar) === '1') {
                    r.mor_referencia_familiar = '0';
                }
            });
        },
        removeRow(i) {
            if (this.rows[i] && !this.rows[i].mor_id) {
                if (this.openOcupanteIdx === i) {
                    this.openOcupanteIdx = null;
                } else if (this.openOcupanteIdx !== null && this.openOcupanteIdx > i) {
                    this.openOcupanteIdx--;
                }
                this.rows.splice(i, 1);
                if (this.rows.length === 0) this.addRow();
            }
        }
    }" class="space-y-4">
            <div x-effect="syncSocioFromRows()"></div>

        <template x-for="(row, idx) in rows" :key="idx">
            <details class="rounded-lg border border-slate-200 bg-slate-50/50 p-4 dark:border-slate-600 dark:bg-slate-900/30"
                     :open="openOcupanteIdx === idx"
                     @toggle="if ($event.target.open) { openOcupanteIdx = idx } else if (openOcupanteIdx === idx) { openOcupanteIdx = null }">
                <summary :aria-expanded="openOcupanteIdx === idx" class="cursor-pointer list-none font-semibold text-slate-700 marker:hidden dark:text-slate-200 [&::-webkit-details-marker]:hidden">
                    <div class="flex items-center justify-between gap-2">
                        <span class="inline-flex items-center gap-2 text-xs">
                            <svg :class="openOcupanteIdx === idx ? 'ocupante-chevron rotate-90 w-3 h-3 text-slate-500 dark:text-slate-400' : 'ocupante-chevron w-3 h-3 text-slate-500 dark:text-slate-400'" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M7.21 14.21a1 1 0 01-1.42-1.42L10.17 8 5.79 3.62a1 1 0 011.42-1.42l5 5a1 1 0 010 1.42l-5 5z" clip-rule="evenodd" />
                            </svg>
                            <span x-text="row.mor_id ? '{{ __('Ocupante') }} #' + row.mor_id : '{{ __('Novo ocupante') }}'"></span>
                            <span x-show="String(row.mor_referencia_familiar) === '1'" class="inline-flex items-center rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">{{ __('Titular') }}</span>
                        </span>
                        <span class="truncate text-right text-xs font-normal text-slate-500 dark:text-slate-400" x-text="(row.mor_nome && row.mor_nome.trim()) ? row.mor_nome : '{{ __('Sem nome informado') }}'"></span>
                    </div>
                </summary>
                <div class="mt-3 space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                <div class="flex justify-end">
                    <button type="button" class="text-xs text-red-600 hover:underline dark:text-red-400" @click="removeRow(idx)" x-show="!row.mor_id && rows.length > 1">{{ __('Remover linha') }}</button>
                </div>
                <input type="hidden" x-bind:name="'ocupantes[' + idx + '][mor_id]'" x-bind:value="row.mor_id != null && row.mor_id !== '' ? row.mor_id : ''">
                <fieldset class="space-y-3">
                    <legend class="v-section-title">{{ __('Dados pessoais') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Nome')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_nome]'" x-model="row.mor_nome" class="v-input mt-1 w-full" maxlength="255">
                        </div>
                        <div>
                            <x-input-label :value="__('Data de nascimento')" />
                            <input type="date" x-bind:name="'ocupantes[' + idx + '][mor_data_nascimento]'" x-model="row.mor_data_nascimento" class="v-input mt-1 w-full">
                        </div>
                        <div>
                            <x-input-label :value="__('Sexo')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_sexo]'" x-model="row.mor_sexo" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(sexoOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Estado civil')" />
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
                    <legend class="v-section-title">{{ __('Vínculo familiar e contato') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Referência familiar (titular)')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_referencia_familiar]'"
                                    x-model="row.mor_referencia_familiar"
                                    @change="if (String(row.mor_referencia_familiar) !== '0') row.mor_parentesco = ''; enforceSingleTitular(idx)"
                                    class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <option value="1">{{ __('Sim') }}</option>
                                <option value="0">{{ __('Não') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ __('Defina apenas uma pessoa como referência familiar.') }}</p>
                        </div>
                        <div>
                            <x-input-label :value="__('Parentesco com o titular')" />
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
                            <x-input-label :value="__('Telefone')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_telefone]'" x-model="row.mor_telefone" class="v-input mt-1 w-full" maxlength="40">
                        </div>
                        <div>
                            <x-input-label :value="__('Naturalidade')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_naturalidade]'" x-model="row.mor_naturalidade" class="v-input mt-1 w-full" maxlength="150" placeholder="{{ __('Cidade/UF') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-section-title">{{ __('Identificação') }}</legend>
                    <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('RG e CPF informados no cadastro (números e datas).') }}</p>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <x-input-label :value="__('CPF')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_cpf]'" x-model="row.mor_cpf" class="v-input mt-1 w-full" maxlength="20">
                        </div>
                        <div>
                            <x-input-label :value="__('RG: número')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_numero]'" x-model="row.mor_rg_numero" class="v-input mt-1 w-full" maxlength="45">
                        </div>
                        <div>
                            <x-input-label :value="__('RG: órgão')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_rg_orgao]'" x-model="row.mor_rg_orgao" class="v-input mt-1 w-full" maxlength="60">
                        </div>
                        <div>
                            <x-input-label :value="__('RG: expedição')" />
                            <input type="date" x-bind:name="'ocupantes[' + idx + '][mor_rg_expedicao]'" x-model="row.mor_rg_expedicao" class="v-input mt-1 w-full">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-section-title">{{ __('Arquivos') }}</legend>
                    <div class="sm:col-span-2" x-data="{
                            fileSummary: '',
                            openPicker() { this.$refs.documentoPessoal.click(); },
                            updateName(event) {
                                const files = event.target.files;
                                if (!files || !files.length) { this.fileSummary = ''; return; }
                                if (files.length === 1) { this.fileSummary = files[0].name; return; }
                                this.fileSummary = files.length + ' {{ __('arquivos selecionados') }}';
                            }
                        }">
                        <x-arquivos-zona
                            variant="ocupante"
                            :accent-border="false"
                            :titulo="__('Arquivos deste ocupante')"
                            :descricao="__('Anexe um ou mais arquivos por pessoa. PDF ou imagem, até 10 MB cada.')"
                        >
                            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400" x-show="(row.documentos_pessoais || []).length">{{ __('Arquivos já enviados') }}</p>
                            <ul class="mb-3 space-y-2" x-show="(row.documentos_pessoais || []).length">
                                <template x-for="doc in (row.documentos_pessoais || [])" :key="doc.id">
                                    <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200/80 bg-slate-50/80 px-3 py-2.5 text-xs dark:border-slate-600 dark:bg-slate-800/60">
                                        <span class="min-w-0 flex-1 break-all font-medium text-slate-800 dark:text-slate-100" x-text="doc.nome || '{{ __('Arquivo') }}'"></span>
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a x-show="doc.download_url" :href="doc.download_url" target="_blank" rel="noopener"
                                               class="inline-flex shrink-0 items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">{{ __('Baixar') }}</a>
                                            <label class="inline-flex shrink-0 items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                                <input type="checkbox" class="rounded border-slate-300 text-red-600 focus:ring-red-500"
                                                       x-bind:name="'ocupantes[' + idx + '][remover_documentos_pessoal][]'"
                                                       :value="doc.id">
                                                <span>{{ __('Remover') }}</span>
                                            </label>
                                        </div>
                                    </li>
                                </template>
                            </ul>
                            <div class="rounded-lg border border-dashed border-slate-200/90 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/40">
                                <x-input-label :value="__('Adicionar arquivos')" class="text-slate-800 dark:text-slate-200" />
                                <input type="file"
                                       x-ref="documentoPessoal"
                                       x-bind:name="'ocupantes[' + idx + '][mor_documentos_pessoal][]'"
                                       accept="image/*,application/pdf"
                                       capture="environment"
                                       multiple
                                       class="sr-only"
                                       @change="updateName($event)">
                                <div class="mt-2 flex flex-wrap items-center gap-3">
                                    <button type="button"
                                            @click="openPicker()"
                                            class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                                        {{ __('Selecionar arquivo(s) ou tirar foto') }}
                                    </button>
                                    <span class="text-xs text-slate-600 dark:text-slate-400" x-text="fileSummary ? ('{{ __('Novo') }}: ' + fileSummary) : '{{ __('Nenhum arquivo selecionado') }}'"></span>
                                </div>
                            </div>
                        </x-arquivos-zona>
                    </div>
                </fieldset>

                <fieldset class="space-y-3 border-t border-slate-200 pt-3 dark:border-slate-700">
                    <legend class="v-section-title">{{ __('Perfil socioeconômico') }}</legend>
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div>
                            <x-input-label :value="__('Escolaridade')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_escolaridade]'" x-model="row.mor_escolaridade" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(esc)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Cor ou raça')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_cor_raca]'" x-model="row.mor_cor_raca" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(corOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Profissão / ocupação declarada')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_profissao]'" x-model="row.mor_profissao" class="v-input mt-1 w-full" maxlength="150">
                        </div>
                        <div>
                            <x-input-label :value="__('Situação no trabalho')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_situacao_trabalho]'" x-model="row.mor_situacao_trabalho" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(trabOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Faixa de renda (salário mínimo)')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_renda_faixa]'" x-model="row.mor_renda_faixa" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(rendaOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Renda formal / informal (pessoa)')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_renda_formal_informal]'" x-model="row.mor_renda_formal_informal" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <template x-for="[k, label] in Object.entries(rfiOpts)" :key="k">
                                    <option :value="k" x-text="label"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <x-input-label :value="__('Tempo com cônjuge / união')" />
                            <input type="text" x-bind:name="'ocupantes[' + idx + '][mor_tempo_uniao_conjuge]'" x-model="row.mor_tempo_uniao_conjuge" class="v-input mt-1 w-full" maxlength="120">
                        </div>
                        <div>
                            <x-input-label :value="__('Ajudou na compra/construção do imóvel')" />
                            <select x-bind:name="'ocupantes[' + idx + '][mor_ajuda_compra_imovel]'" x-model="row.mor_ajuda_compra_imovel" class="v-select mt-1 w-full">
                                <option value="">{{ __('Selecionar') }}</option>
                                <option value="sim">{{ __('Sim') }}</option>
                                <option value="nao">{{ __('Não') }}</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <div>
                    <x-input-label :value="__('Observações')" />
                    <textarea x-bind:name="'ocupantes[' + idx + '][mor_observacao]'" x-model="row.mor_observacao" rows="2" class="v-input mt-1 w-full"></textarea>
                </div>
                </div>
            </details>
        </template>
            <button type="button" @click="addRow()" class="v-btn-compact v-btn-compact--blue text-sm">
                + {{ __('Adicionar morador') }}
            </button>
        </div>
    </div>
</x-ui.disclosure>
