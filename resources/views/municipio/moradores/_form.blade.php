@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
    $sexo = config('visitaai_socioeconomico.sexo_opcoes', []);
    $ec = config('visitaai_socioeconomico.estado_civil_opcoes', []);
    $par = config('visitaai_socioeconomico.parentesco_opcoes', []);
    $rfi = config('visitaai_socioeconomico.renda_formal_informal_opcoes', []);
@endphp

<div class="space-y-5">
    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Dados pessoais') }}</legend>
        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <x-input-label for="mor_nome" :value="__('Nome')" />
                <x-text-input id="mor_nome" name="mor_nome" type="text" class="mt-1 block w-full" :value="old('mor_nome', $morador->mor_nome)" />
                <x-input-error :messages="$errors->get('mor_nome')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_data_nascimento" :value="__('Data de nascimento')" />
                <x-text-input id="mor_data_nascimento" name="mor_data_nascimento" type="date" class="mt-1 block w-full" :value="old('mor_data_nascimento', optional($morador->mor_data_nascimento)->format('Y-m-d'))" />
                <x-input-error :messages="$errors->get('mor_data_nascimento')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_sexo" :value="__('Sexo')" />
                <select id="mor_sexo" name="mor_sexo" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($sexo as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_sexo', $morador->mor_sexo) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_sexo')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_estado_civil" :value="__('Estado civil')" />
                <select id="mor_estado_civil" name="mor_estado_civil" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($ec as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_estado_civil', $morador->mor_estado_civil) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_estado_civil')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_cor_raca" :value="__('Cor ou raça (autodeclarada)')" />
                <select id="mor_cor_raca" name="mor_cor_raca" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($cor as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_cor_raca', $morador->mor_cor_raca) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_cor_raca')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_naturalidade" :value="__('Naturalidade')" />
                <x-text-input id="mor_naturalidade" name="mor_naturalidade" type="text" class="mt-1 block w-full" :value="old('mor_naturalidade', $morador->mor_naturalidade)" />
                <x-input-error :messages="$errors->get('mor_naturalidade')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_telefone" :value="__('Telefone')" />
                <x-text-input id="mor_telefone" name="mor_telefone" type="text" class="mt-1 block w-full" :value="old('mor_telefone', $morador->mor_telefone)" />
                <x-input-error :messages="$errors->get('mor_telefone')" class="mt-2" />
            </div>
            <div class="lg:col-span-2">
                <div class="flex items-center gap-2 pt-1">
                    <input type="hidden" name="mor_referencia_familiar" value="0">
                    <input type="checkbox" id="mor_referencia_familiar" name="mor_referencia_familiar" value="1" class="rounded border-slate-300" @checked(old('mor_referencia_familiar', $morador->mor_referencia_familiar))>
                    <x-input-label for="mor_referencia_familiar" :value="__('Referência familiar (titular)')" class="!mb-0" />
                </div>
            </div>
            <div class="lg:col-span-2">
                <x-input-label for="mor_parentesco" :value="__('Parentesco com o titular')" />
                <select id="mor_parentesco" name="mor_parentesco" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($par as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_parentesco', $morador->mor_parentesco) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_parentesco')" class="mt-2" />
            </div>
            <div class="lg:col-span-2">
                <x-input-label for="mor_observacao" :value="__('Observações')" />
                <textarea id="mor_observacao" name="mor_observacao" rows="3" class="v-input mt-1">{{ old('mor_observacao', $morador->mor_observacao) }}</textarea>
                <x-input-error :messages="$errors->get('mor_observacao')" class="mt-2" />
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Escolaridade e estudos') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="mor_escolaridade" :value="__('Escolaridade')" />
                <select id="mor_escolaridade" name="mor_escolaridade" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($esc as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_escolaridade', $morador->mor_escolaridade) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_escolaridade')" class="mt-2" />
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Renda e trabalho') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-input-label for="mor_profissao" :value="__('Profissão')" />
                <x-text-input id="mor_profissao" name="mor_profissao" type="text" class="mt-1 block w-full" :value="old('mor_profissao', $morador->mor_profissao)" />
                <x-input-error :messages="$errors->get('mor_profissao')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_situacao_trabalho" :value="__('Situação no trabalho')" />
                <select id="mor_situacao_trabalho" name="mor_situacao_trabalho" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($trab as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_situacao_trabalho', $morador->mor_situacao_trabalho) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_situacao_trabalho')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_renda_faixa" :value="__('Faixa de renda (referência salário mínimo)')" />
                <select id="mor_renda_faixa" name="mor_renda_faixa" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($renda as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_renda_faixa', $morador->mor_renda_faixa) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_renda_faixa')" class="mt-2" />
            </div>
            <div class="lg:col-span-3">
                <x-input-label for="mor_renda_formal_informal" :value="__('Renda formal / informal')" />
                <select id="mor_renda_formal_informal" name="mor_renda_formal_informal" class="v-select mt-1 w-full">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($rfi as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_renda_formal_informal', $morador->mor_renda_formal_informal) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_renda_formal_informal')" class="mt-2" />
            </div>
        </div>
    </fieldset>

    <fieldset class="space-y-4 rounded-lg border border-slate-200 bg-white/70 p-4 dark:border-slate-700 dark:bg-slate-900/30">
        <legend class="px-1 text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Documentos pessoais') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-input-label for="mor_rg_numero" :value="__('RG (número)')" />
                <x-text-input id="mor_rg_numero" name="mor_rg_numero" type="text" class="mt-1 block w-full" :value="old('mor_rg_numero', $morador->mor_rg_numero)" />
            </div>
            <div>
                <x-input-label for="mor_rg_orgao" :value="__('RG (órgão)')" />
                <x-text-input id="mor_rg_orgao" name="mor_rg_orgao" type="text" class="mt-1 block w-full" :value="old('mor_rg_orgao', $morador->mor_rg_orgao)" />
            </div>
            <div class="lg:col-span-3">
                <x-input-label for="mor_cpf" :value="__('CPF')" />
                <x-text-input id="mor_cpf" name="mor_cpf" type="text" class="mt-1 block w-full" :value="old('mor_cpf', $morador->mor_cpf)" />
            </div>
            <div class="lg:col-span-3 space-y-3 rounded-lg border border-slate-200 bg-slate-50/60 p-3 dark:border-slate-700 dark:bg-slate-900/40"
                 x-data="{
                    fileName: '',
                    openPicker() { this.$refs.documentoPessoal.click(); },
                    updateName(event) { this.fileName = event.target.files && event.target.files.length ? event.target.files[0].name : ''; }
                 }">
                <x-input-label for="mor_documento_pessoal" :value="__('Documento pessoal (arquivo ou foto)')" />
                <input
                    x-ref="documentoPessoal"
                    id="mor_documento_pessoal"
                    name="mor_documento_pessoal"
                    type="file"
                    accept="image/*,application/pdf"
                    capture="environment"
                    class="sr-only"
                    @change="updateName($event)"
                >
                <div class="flex flex-wrap items-center gap-3">
                    <button type="button" @click="openPicker()" class="inline-flex items-center rounded-md border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                        {{ __('Selecionar arquivo ou tirar foto') }}
                    </button>
                    <span class="text-xs text-slate-600 dark:text-slate-400" x-text="fileName || '{{ __('Nenhum arquivo selecionado') }}'"></span>
                </div>
                <p class="text-xs text-slate-600 dark:text-slate-400">{{ __('Você pode selecionar um arquivo do aparelho ou tirar uma foto no celular. Formatos: PDF, JPG, PNG, WEBP, HEIC. Limite: 10 MB.') }}</p>
                <x-input-error :messages="$errors->get('mor_documento_pessoal')" class="mt-1" />

                @if($morador->exists && $morador->mor_documento_pessoal_path)
                    <div class="flex flex-wrap items-center gap-3 text-xs">
                        <a href="{{ route($profile . '.locais.moradores.documento-pessoal', [$local, $morador]) }}"
                           class="inline-flex items-center rounded-md border border-slate-300 bg-white px-2.5 py-1.5 font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ __('Baixar documento atual') }}
                        </a>
                        <label class="inline-flex items-center gap-2 text-slate-700 dark:text-slate-300">
                            <input type="checkbox" name="remover_documento_pessoal" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                            <span>{{ __('Remover documento atual') }}</span>
                        </label>
                    </div>
                    <x-input-error :messages="$errors->get('remover_documento_pessoal')" class="mt-1" />
                @endif
            </div>
        </div>
    </fieldset>
</div>
