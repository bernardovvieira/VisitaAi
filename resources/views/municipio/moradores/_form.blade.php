@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
    $cor = config('visitaai_municipio.cor_raca_opcoes', []);
    $trab = config('visitaai_municipio.situacao_trabalho_opcoes', []);
@endphp

<x-lgpd.aviso context="ocupantes_cadastro" class="mb-4" :compact="true" />

<div class="space-y-4">
    <div>
        <x-input-label for="mor_nome" :value="__('Nome (opcional)')" />
        <x-text-input id="mor_nome" name="mor_nome" type="text" class="mt-1 block w-full" :value="old('mor_nome', $morador->mor_nome)" />
        <x-input-error :messages="$errors->get('mor_nome')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_data_nascimento" :value="__('Data de nascimento (opcional)')" />
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
    <div>
        <x-input-label for="mor_observacao" :value="__('Observações (opcional)')" />
        <textarea id="mor_observacao" name="mor_observacao" rows="3" class="v-input mt-1">{{ old('mor_observacao', $morador->mor_observacao) }}</textarea>
        <x-input-error :messages="$errors->get('mor_observacao')" class="mt-2" />
    </div>
</div>
