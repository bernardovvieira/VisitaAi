@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $sexo = config('visitaai_socioeconomico.sexo_opcoes', []);
    $ec = config('visitaai_socioeconomico.estado_civil_opcoes', []);
    $par = config('visitaai_socioeconomico.parentesco_opcoes', []);
    $rfi = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
    $relacaoFamiliar = old('mor_referencia_familiar', $morador->mor_referencia_familiar) ? 'titular' : (filled(old('mor_parentesco', $morador->mor_parentesco)) ? 'par:' . old('mor_parentesco', $morador->mor_parentesco) : '');
@endphp

<div class="space-y-5">
    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Dados pessoais') }}</legend>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-form-field name="mor_nome" :label="__('Nome')">
                    <x-text-input id="mor_nome" name="mor_nome" type="text" class="mt-1 block w-full" :value="old('mor_nome', $morador->mor_nome)" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_data_nascimento" :label="__('Data de nascimento')">
                    <x-text-input id="mor_data_nascimento" name="mor_data_nascimento" type="date" class="mt-1 block w-full" :value="old('mor_data_nascimento', optional($morador->mor_data_nascimento)->format('Y-m-d'))" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_sexo" :label="__('Sexo')">
                    <select id="mor_sexo" name="mor_sexo" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($sexo as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_sexo', $morador->mor_sexo) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_estado_civil" :label="__('Estado civil')">
                    <select id="mor_estado_civil" name="mor_estado_civil" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($ec as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_estado_civil', $morador->mor_estado_civil) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_cor_raca" :label="__('Cor ou raça (autodeclarada)')">
                    <select id="mor_cor_raca" name="mor_cor_raca" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($cor as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_cor_raca', $morador->mor_cor_raca) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_naturalidade" :label="__('Naturalidade')">
                    <x-text-input id="mor_naturalidade" name="mor_naturalidade" type="text" class="mt-1 block w-full" :value="old('mor_naturalidade', $morador->mor_naturalidade)" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_telefone" :label="__('Telefone')">
                    <x-text-input id="mor_telefone" name="mor_telefone" type="text" class="mt-1 block w-full" :value="old('mor_telefone', $morador->mor_telefone)" />
                </x-form-field>
            </div>
            <div x-data="{ relacaoFamiliar: @js($relacaoFamiliar) }">
                <x-input-label for="mor_relacao_familiar" :value="__('Relação familiar')" />
                <select id="mor_relacao_familiar" x-model="relacaoFamiliar" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    <option value="titular">{{ __('Titular da ficha') }}</option>
                    @foreach($par as $k => $label)
                        <option value="par:{{ $k }}">{{ $label }}</option>
                    @endforeach
                </select>
                <input type="hidden" name="mor_referencia_familiar" x-bind:value="relacaoFamiliar === 'titular' ? 1 : 0">
                <input type="hidden" name="mor_parentesco" x-bind:value="relacaoFamiliar && relacaoFamiliar.startsWith('par:') ? relacaoFamiliar.slice(4) : ''">
            </div>
            <div class="lg:col-span-3">
                <x-form-field name="mor_observacao" :label="__('Observações')">
                    <textarea id="mor_observacao" name="mor_observacao" rows="3" class="v-input mt-1">{{ old('mor_observacao', $morador->mor_observacao) }}</textarea>
                </x-form-field>
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Escolaridade e estudos') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <x-form-field name="mor_escolaridade" :label="__('Escolaridade')">
                    <select id="mor_escolaridade" name="mor_escolaridade" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($esc as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_escolaridade', $morador->mor_escolaridade) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Renda e trabalho') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-form-field name="mor_profissao" :label="__('Profissão')">
                    <x-text-input id="mor_profissao" name="mor_profissao" type="text" class="mt-1 block w-full" :value="old('mor_profissao', $morador->mor_profissao)" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_situacao_trabalho" :label="__('Situação no trabalho')">
                    <select id="mor_situacao_trabalho" name="mor_situacao_trabalho" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($trab as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_situacao_trabalho', $morador->mor_situacao_trabalho) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_renda_faixa" :label="__('Faixa de renda (referência salário mínimo)')">
                    <select id="mor_renda_faixa" name="mor_renda_faixa" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($renda as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_renda_faixa', $morador->mor_renda_faixa) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
            <div class="lg:col-span-3">
                <x-form-field name="mor_renda_formal_informal" :label="__('Renda formal / informal')">
                    <select id="mor_renda_formal_informal" name="mor_renda_formal_informal" class="v-select mt-1 w-full">
                        <option value="">{{ __('Selecionar') }}</option>
                        @foreach($rfi as $k => $label)
                            <option value="{{ $k }}" @selected(old('mor_renda_formal_informal', $morador->mor_renda_formal_informal) === $k)>{{ $label }}</option>
                        @endforeach
                    </select>
                </x-form-field>
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Identificação') }}</legend>
        <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('RG e CPF informados no cadastro (números e datas).') }}</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-form-field name="mor_rg_numero" :label="__('RG (número)')">
                    <x-text-input id="mor_rg_numero" name="mor_rg_numero" type="text" class="mt-1 block w-full" :value="old('mor_rg_numero', $morador->mor_rg_numero)" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_rg_orgao" :label="__('RG (órgão)')">
                    <x-text-input id="mor_rg_orgao" name="mor_rg_orgao" type="text" class="mt-1 block w-full" :value="old('mor_rg_orgao', $morador->mor_rg_orgao)" />
                </x-form-field>
            </div>
            <div>
                <x-form-field name="mor_rg_expedicao" :label="__('RG (expedição)')">
                    <x-text-input id="mor_rg_expedicao" name="mor_rg_expedicao" type="date" class="mt-1 block w-full" :value="old('mor_rg_expedicao', optional($morador->mor_rg_expedicao)->format('Y-m-d'))" />
                </x-form-field>
            </div>
            <div class="lg:col-span-3">
                <x-form-field name="mor_cpf" :label="__('CPF')">
                    <x-text-input id="mor_cpf" name="mor_cpf" type="text" class="mt-1 block w-full" :value="old('mor_cpf', $morador->mor_cpf)" />
                </x-form-field>
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Arquivos') }}</legend>
        <div x-data="{
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
                @if($morador->exists && $morador->documentosPessoais->isNotEmpty())
                    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">{{ __('Arquivos já enviados') }}</p>
                    <ul class="mb-3 space-y-2">
                        @foreach($morador->documentosPessoais as $doc)
                            <li class="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-200/80 bg-slate-50/80 px-3 py-2.5 text-xs dark:border-slate-600 dark:bg-slate-800/60">
                                <span class="min-w-0 flex-1 break-all font-medium text-slate-800 dark:text-slate-100">{{ $doc->original_name ?: __('Arquivo') }}</span>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route($profile . '.locais.moradores.documento-pessoal', [$local, $morador, $doc]) }}"
                                       class="inline-flex shrink-0 items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-900 dark:text-slate-200 dark:hover:bg-slate-800">
                                        {{ __('Baixar') }}
                                    </a>
                                    <label class="inline-flex shrink-0 items-center gap-1.5 text-slate-700 dark:text-slate-300">
                                        <input type="checkbox" name="remover_documentos_pessoal[]" value="{{ $doc->id }}" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                        <span>{{ __('Remover') }}</span>
                                    </label>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    @foreach($errors->keys() as $errKey)
                        @if(str_starts_with((string) $errKey, 'remover_documentos_pessoal'))
                            <x-input-error :messages="$errors->get($errKey)" class="mt-1" />
                        @endif
                    @endforeach
                @endif
                <div class="rounded-lg border border-dashed border-slate-200/90 bg-slate-50/50 p-3 dark:border-slate-600 dark:bg-slate-800/40">
                    <x-input-label for="mor_documentos_pessoal" :value="__('Adicionar arquivos')" class="text-slate-800 dark:text-slate-200" />
                    <input
                        x-ref="documentoPessoal"
                        id="mor_documentos_pessoal"
                        name="mor_documentos_pessoal[]"
                        type="file"
                        accept="image/*,application/pdf"
                        capture="environment"
                        multiple
                        class="sr-only"
                        @change="updateName($event)"
                    >
                    <div class="mt-2 flex flex-wrap items-center gap-3">
                        <button type="button" @click="openPicker()" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ __('Selecionar arquivo(s) ou tirar foto') }}
                        </button>
                        <span class="text-xs text-slate-600 dark:text-slate-400" x-text="fileSummary || '{{ __('Nenhum arquivo selecionado') }}'"></span>
                    </div>
                </div>
                @foreach($errors->keys() as $errKey)
                    @if(str_starts_with((string) $errKey, 'mor_documentos_pessoal'))
                        <x-input-error :messages="$errors->get($errKey)" class="mt-1" />
                    @endif
                @endforeach
            </x-arquivos-zona>
        </div>
    </fieldset>
</div>
