{{-- Ficha socioeconômica: imóvel, físico, infra, terreno, histórico, finalização --}}
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
@endphp

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['imovel_caracteristicas'] ?? __('5. Características do imóvel') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Uso do imóvel') }}</label>
            <select name="socio[uso_imovel]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.uso_imovel_socio_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('uso_imovel') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Situação da posse') }}</label>
            <select name="socio[situacao_posse]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.situacao_posse_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('situacao_posse') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Material predominante') }}</label>
            <select name="socio[material_predominante]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.material_predominante_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('material_predominante') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Condição da edificação') }}</label>
            <select name="socio[condicao_edificacao]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.condicao_edificacao_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('condicao_edificacao') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nº de cômodos') }}</label>
            <input type="number" name="socio[num_comodos]" value="{{ $sv('num_comodos') }}" class="v-input mt-1 w-full" min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nº de quartos') }}</label>
            <input type="number" name="socio[num_quartos]" value="{{ $sv('num_quartos') }}" class="v-input mt-1 w-full" min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Área externa / quintal') }}</label>
            <select name="socio[area_externa]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.area_externa_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('area_externa') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Área livre / quintal (detalhe)') }}</label>
            <input type="text" name="socio[area_livre]" value="{{ $sv('area_livre') }}" class="v-input mt-1 w-full" maxlength="255">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Observações do imóvel') }}</label>
            <textarea name="socio[observacoes_imovel]" rows="2" class="v-input mt-1 w-full">{{ $sv('observacoes_imovel') }}</textarea>
        </div>
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['cadastro_fisico'] ?? __('6. Cadastro físico') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tipologia') }}</label>
            <select name="socio[tipologia]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.tipologia_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('tipologia') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tipo / implantação') }}</label>
            <select name="socio[tipo_implantacao]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.tipo_implantacao_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('tipo_implantacao') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Posição no lote') }}</label>
            <select name="socio[posicao_lote]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.posicao_lote_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('posicao_lote') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Nº de pavimentos') }}</label>
            <input type="number" name="socio[num_pavimentos]" value="{{ $sv('num_pavimentos') }}" class="v-input mt-1 w-full" min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Banheiros: dentro') }}</label>
            <input type="number" name="socio[banheiro_dentro]" value="{{ $sv('banheiro_dentro') }}" class="v-input mt-1 w-full" min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Banheiros: fora') }}</label>
            <input type="number" name="socio[banheiro_fora]" value="{{ $sv('banheiro_fora') }}" class="v-input mt-1 w-full" min="0">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Banheiro compartilha com outra família') }}</label>
            <select name="socio[banheiro_compartilha]" class="v-select mt-1 w-full">
                <option value="">{{ __('Não informado') }}</option>
                <option value="1" @selected($sv('banheiro_compartilha') === '1' || $sv('banheiro_compartilha') === true || $sv('banheiro_compartilha') === 1)>{{ __('Sim') }}</option>
                <option value="0" @selected($sv('banheiro_compartilha') === '0' || $sv('banheiro_compartilha') === false || $sv('banheiro_compartilha') === 0)>{{ __('Não') }}</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Acesso ao imóvel') }}</label>
            <select name="socio[acesso_imovel]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.acesso_imovel_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('acesso_imovel') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Entrada da casa voltada para') }}</label>
            <select name="socio[entrada_para]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.entrada_para_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('entrada_para') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['infraestrutura'] ?? __('7. Infraestrutura e serviços') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        @foreach([
            'abastecimento_agua' => ['cfg' => 'infra_sim_nao_redes_opcoes', 'label' => __('Abastecimento de água')],
            'energia_eletrica' => ['cfg' => 'infra_energia_opcoes', 'label' => __('Energia elétrica')],
            'esgoto' => ['cfg' => 'infra_esgoto_opcoes', 'label' => __('Esgoto')],
            'coleta_lixo' => ['cfg' => 'infra_lixo_opcoes', 'label' => __('Coleta de lixo')],
            'pavimentacao' => ['cfg' => 'infra_pavimentacao_opcoes', 'label' => __('Pavimentação da rua')],
        ] as $field => $meta)
            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ $meta['label'] }}</label>
                <select name="socio[{{ $field }}]" class="v-select mt-1 w-full">
                    @foreach(config('visitaai_socioeconomico.'.$meta['cfg'], []) as $k => $lab)
                        <option value="{{ $k }}" @selected($sv($field) === $k)>{{ $lab }}</option>
                    @endforeach
                </select>
            </div>
        @endforeach
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['terreno'] ?? __('8. Terreno e tempo de residência') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Situação do terreno') }}</label>
            <select name="socio[situacao_terreno]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.situacao_terreno_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('situacao_terreno') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Posse da área') }}</label>
            <select name="socio[posse_area]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.posse_area_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('posse_area') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Tempo de residência (texto)') }}</label>
            <input type="text" name="socio[tempo_residencia_texto]" value="{{ $sv('tempo_residencia_texto') }}" class="v-input mt-1 w-full" maxlength="150">
        </div>
    </div>
</x-ui.disclosure>

<x-ui.disclosure variant="muted-card-simple" :open="false">
    <x-slot name="summary">
        <span class="border-b border-dotted border-slate-400 pb-px dark:border-slate-500">{{ $t['historico'] ?? __('9. Histórico da posse') }}</span>
    </x-slot>
    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Data de ocupação / compra') }}</label>
            <input type="date" name="socio[data_ocupacao]" value="{{ $sv('data_ocupacao') }}" class="v-input mt-1 w-full">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Houve compra e venda') }}</label>
            <select name="socio[houve_compra_venda]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.sim_nao_curto_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('houve_compra_venda') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Forma de aquisição / como ocupou') }}</label>
            <textarea name="socio[forma_aquisicao]" rows="2" class="v-input mt-1 w-full">{{ $sv('forma_aquisicao') }}</textarea>
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Como ocupou o imóvel') }}</label>
            <textarea name="socio[como_ocupou]" rows="2" class="v-input mt-1 w-full">{{ $sv('como_ocupou') }}</textarea>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Escritura / registro') }}</label>
            <select name="socio[escritura]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.escritura_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('escritura') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Contrato de promessa de compra e venda') }}</label>
            <select name="socio[contrato_promessa]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.sim_nao_curto_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('contrato_promessa') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Documento quitado') }}</label>
            <select name="socio[documento_quitado]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.sim_nao_curto_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('documento_quitado') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Sabe onde reside o vendedor / anterior') }}</label>
            <select name="socio[sabe_local_vendedor]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.sim_nao_curto_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('sabe_local_vendedor') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Paga IPTU') }}</label>
            <select name="socio[paga_iptu]" class="v-select mt-1 w-full">
                @foreach(config('visitaai_socioeconomico.sim_nao_curto_opcoes', []) as $k => $lab)
                    <option value="{{ $k }}" @selected($sv('paga_iptu') === $k)>{{ $lab }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('IPTU: desde / observação') }}</label>
            <input type="number" name="socio[iptu_desde]" value="{{ $sv('iptu_desde') }}" class="v-input mt-1 w-full" min="1900" max="2100" step="1" placeholder="{{ __('Ex.: 2020') }}">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Proprietário anterior: nome') }}</label>
            <input type="text" name="socio[proprietario_anterior_nome]" value="{{ $sv('proprietario_anterior_nome') }}" class="v-input mt-1 w-full" maxlength="255">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Proprietário anterior: CPF / doc.') }}</label>
            <input type="text" name="socio[proprietario_anterior_doc]" value="{{ $sv('proprietario_anterior_doc') }}" class="v-input mt-1 w-full" maxlength="30">
        </div>
        <div class="sm:col-span-2">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-300">{{ __('Situação legal (observações)') }}</label>
            <textarea name="socio[situacao_legal_obs]" rows="2" class="v-input mt-1 w-full">{{ $sv('situacao_legal_obs') }}</textarea>
        </div>
    </div>
</x-ui.disclosure>

