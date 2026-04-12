{{-- Ficha socioeconômica: entrevista, economia e proprietário (antes dos moradores) --}}
@php
    /** @var \App\Models\Local|null $local */
    $sf = old('socio');
    if (! is_array($sf)) {
        $sf = [];
    }
    if (count($sf) === 0 && isset($local) && $local !== null) {
        $local->loadMissing('socioeconomico');
        if ($local->socioeconomico) {
            $sf = $local->socioeconomico->toFormArray();
        }
    }
    $sv = fn (string $k) => old('socio.'.$k, $sf[$k] ?? '');

    $t = config('visitaai_socioeconomico.secao_titulos', []);
    $disc = 'border-t border-gray-200 pt-6 mt-2 dark:border-gray-600';
@endphp

<x-ui.callout variant="amber" class="v-alert-erp border-amber-200/60 dark:border-amber-900/40 mb-4" role="alert">
    <p class="text-sm text-amber-900/90 dark:text-amber-200/90">{{ __('Ficha socioeconômica complementar ao cadastro territorial e às obrigações de sistemas oficiais (ex.: CadÚnico, e-SUS). Informe apenas o necessário (minimização, LGPD). CPF/RG são opcionais e sensíveis.') }}</p>
</x-ui.callout>

<input type="hidden" name="socio[telefone_contato]" value="{{ $sv('telefone_contato') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[posicao_entrevistado]" value="{{ $sv('posicao_entrevistado') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[n_moradores_declarado]" value="{{ $sv('n_moradores_declarado') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[renda_formal_informal]" value="{{ $sv('renda_formal_informal') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[renda_familiar_faixa]" value="{{ $sv('renda_familiar_faixa') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[principal_fonte_renda]" value="{{ $sv('principal_fonte_renda') }}" data-autofill-from-ocupantes="1">
<input type="hidden" name="socio[qtd_contribuintes]" value="{{ $sv('qtd_contribuintes') }}" data-autofill-from-ocupantes="1">

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['entrevista'] ?? __('1. Entrevista e domicílio') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 {{ $disc }} border-0 pt-0 mt-0">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Data da entrevista') }}</label>
            <input type="date" name="socio[data_entrevista]" value="{{ $sv('data_entrevista') }}" class="v-input mt-1 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Condição da moradia') }}</label>
            <select name="socio[condicao_casa]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.condicao_casa_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('condicao_casa') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['economia'] ?? __('2. Economia do grupo familiar') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Gastos mensais aproximados (faixa)') }}</label>
            <select name="socio[gastos_mensais_faixa]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.gastos_mensais_faixa_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('gastos_mensais_faixa') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Benefícios sociais (Bolsa Família, BPC, outros)') }}</label>
            <textarea name="socio[beneficios_sociais]" rows="2" class="v-input mt-1 w-full">{{ $sv('beneficios_sociais') }}</textarea>
        </div>
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['proprietario'] ?? __('3. Proprietário (se aluguel / cedido)') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nome do proprietário') }}</label>
            <input type="text" name="socio[proprietario_nome]" value="{{ $sv('proprietario_nome') }}" class="v-input mt-1 w-full" maxlength="255">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Telefone do proprietário') }}</label>
            <input type="text" name="socio[proprietario_telefone]" value="{{ $sv('proprietario_telefone') }}" class="v-input mt-1 w-full" maxlength="45">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Endereço do proprietário') }}</label>
            <input type="text" name="socio[proprietario_endereco]" value="{{ $sv('proprietario_endereco') }}" class="v-input mt-1 w-full" maxlength="255">
        </div>
    </div>
</x-ui.disclosure>
