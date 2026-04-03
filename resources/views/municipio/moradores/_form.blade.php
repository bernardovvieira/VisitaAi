@php
    $esc = config('visitaai_municipio.escolaridade_opcoes', []);
    $renda = config('visitaai_municipio.renda_faixa_opcoes', []);
@endphp

<div class="space-y-4">
    <div>
        <x-input-label for="mor_nome" value="Nome (opcional)" />
        <x-text-input id="mor_nome" name="mor_nome" type="text" class="mt-1 block w-full" :value="old('mor_nome', $morador->mor_nome)" />
        <x-input-error :messages="$errors->get('mor_nome')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_data_nascimento" value="Data de nascimento (opcional)" />
        <x-text-input id="mor_data_nascimento" name="mor_data_nascimento" type="date" class="mt-1 block w-full" :value="old('mor_data_nascimento', optional($morador->mor_data_nascimento)->format('Y-m-d'))" />
        <x-input-error :messages="$errors->get('mor_data_nascimento')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_escolaridade" value="Escolaridade" />
        <select id="mor_escolaridade" name="mor_escolaridade" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
            <option value="">{{ __('— Selecionar —') }}</option>
            @foreach($esc as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_escolaridade', $morador->mor_escolaridade) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_escolaridade')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_renda_faixa" value="Faixa de renda (referência salário mínimo)" />
        <select id="mor_renda_faixa" name="mor_renda_faixa" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">
            <option value="">{{ __('— Selecionar —') }}</option>
            @foreach($renda as $k => $label)
                <option value="{{ $k }}" @selected(old('mor_renda_faixa', $morador->mor_renda_faixa) === $k)>{{ $label }}</option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('mor_renda_faixa')" class="mt-2" />
    </div>
    <div>
        <x-input-label for="mor_observacao" value="Observações (opcional)" />
        <textarea id="mor_observacao" name="mor_observacao" rows="3" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200">{{ old('mor_observacao', $morador->mor_observacao) }}</textarea>
        <x-input-error :messages="$errors->get('mor_observacao')" class="mt-2" />
    </div>
</div>
