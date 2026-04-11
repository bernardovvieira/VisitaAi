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
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
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
        <x-input-label for="mor_escolaridade" :value="__('Escolaridade')" />
        <select id="mor_escolaridade" name="mor_escolaridade" class="v-select mt-1">
            <option value="">{{ __('Selecionar') }}</option>
            @foreach($esc as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_escolaridade', $morador->mor_escolaridade) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_escolaridade')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_renda_faixa" :value="__('Faixa de renda (referência salário mínimo)')" />
        <select id="mor_renda_faixa" name="mor_renda_faixa" class="v-select mt-1">
            <option value="">{{ __('Selecionar') }}</option>
            @foreach($renda as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_renda_faixa', $morador->mor_renda_faixa) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_renda_faixa')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_cor_raca" :value="__('Cor ou raça (autodeclarada)')" />
        <select id="mor_cor_raca" name="mor_cor_raca" class="v-select mt-1">
            <option value="">{{ __('Selecionar') }}</option>
            @foreach($cor as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_cor_raca', $morador->mor_cor_raca) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_cor_raca')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_situacao_trabalho" :value="__('Situação no trabalho')" />
        <select id="mor_situacao_trabalho" name="mor_situacao_trabalho" class="v-select mt-1">
            <option value="">{{ __('Selecionar') }}</option>
            @foreach($trab as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_situacao_trabalho', $morador->mor_situacao_trabalho) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_situacao_trabalho')" class="mt-2" />
    </div>
    <div class="lg:col-span-2">
        <x-input-label for="mor_observacao" :value="__('Observações')" />
        <textarea id="mor_observacao" name="mor_observacao" rows="3" class="v-input mt-1">{{ old('mor_observacao', $morador->mor_observacao) }}</textarea>
        <x-input-error :messages="$errors->get('mor_observacao')" class="mt-2" />
    </div>
    </div>

    <fieldset class="space-y-4 border-t border-slate-200 pt-4 dark:border-slate-600">
        <legend class="text-sm font-semibold text-slate-800 dark:text-slate-200">{{ __('Ficha socioeconômica') }}</legend>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <x-input-label for="mor_sexo" :value="__('Sexo')" />
                <select id="mor_sexo" name="mor_sexo" class="v-select mt-1">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($sexo as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_sexo', $morador->mor_sexo) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_sexo')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_estado_civil" :value="__('Estado civil')" />
                <select id="mor_estado_civil" name="mor_estado_civil" class="v-select mt-1">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($ec as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_estado_civil', $morador->mor_estado_civil) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_estado_civil')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_parentesco" :value="__('Parentesco com o titular')" />
                <select id="mor_parentesco" name="mor_parentesco" class="v-select mt-1">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($par as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_parentesco', $morador->mor_parentesco) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_parentesco')" class="mt-2" />
            </div>
            <div class="flex items-center gap-2 pt-6">
                <input type="hidden" name="mor_referencia_familiar" value="0">
                <input type="checkbox" id="mor_referencia_familiar" name="mor_referencia_familiar" value="1" class="rounded border-slate-300" @checked(old('mor_referencia_familiar', $morador->mor_referencia_familiar))>
                <x-input-label for="mor_referencia_familiar" :value="__('Referência familiar (titular)')" class="!mb-0" />
            </div>
            <div>
                <x-input-label for="mor_naturalidade" :value="__('Naturalidade')" />
                <x-text-input id="mor_naturalidade" name="mor_naturalidade" type="text" class="mt-1 block w-full" :value="old('mor_naturalidade', $morador->mor_naturalidade)" />
                <x-input-error :messages="$errors->get('mor_naturalidade')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_profissao" :value="__('Profissão')" />
                <x-text-input id="mor_profissao" name="mor_profissao" type="text" class="mt-1 block w-full" :value="old('mor_profissao', $morador->mor_profissao)" />
                <x-input-error :messages="$errors->get('mor_profissao')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_telefone" :value="__('Telefone')" />
                <x-text-input id="mor_telefone" name="mor_telefone" type="text" class="mt-1 block w-full" :value="old('mor_telefone', $morador->mor_telefone)" />
                <x-input-error :messages="$errors->get('mor_telefone')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_renda_formal_informal" :value="__('Renda formal / informal')" />
                <select id="mor_renda_formal_informal" name="mor_renda_formal_informal" class="v-select mt-1">
                    <option value="">{{ __('Selecionar') }}</option>
                    @foreach($rfi as $k => $label)
                        <option value="{{ $k }}" @selected(old('mor_renda_formal_informal', $morador->mor_renda_formal_informal) === $k)>{{ $label }}</option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('mor_renda_formal_informal')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="mor_rg_numero" :value="__('RG (número)')" />
                <x-text-input id="mor_rg_numero" name="mor_rg_numero" type="text" class="mt-1 block w-full" :value="old('mor_rg_numero', $morador->mor_rg_numero)" />
            </div>
            <div>
                <x-input-label for="mor_rg_orgao" :value="__('RG (órgão)')" />
                <x-text-input id="mor_rg_orgao" name="mor_rg_orgao" type="text" class="mt-1 block w-full" :value="old('mor_rg_orgao', $morador->mor_rg_orgao)" />
            </div>
            <div class="sm:col-span-2 lg:col-span-3">
                <x-input-label for="mor_cpf" :value="__('CPF')" />
                <x-text-input id="mor_cpf" name="mor_cpf" type="text" class="mt-1 block w-full" :value="old('mor_cpf', $morador->mor_cpf)" />
            </div>
        </div>
    </fieldset>
</div>
