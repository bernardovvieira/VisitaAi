<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Rule;

trait BuildsSocioeconomicoRules
{
    /**
     * @return array<string, mixed>
     */
    protected function socioeconomicoFormRules(): array
    {
        $sk = fn (string $cfgKey) => array_keys(config('visitaai_socioeconomico.'.$cfgKey, []));
        $rendaFam = array_keys(config('visitaai_municipio.renda_faixa_opcoes', []));

        $in = fn (string $cfgKey) => ['nullable', 'string', Rule::in($sk($cfgKey))];

        $rules = [
            'socio' => ['nullable', 'array'],
            'socio.data_entrevista' => ['nullable', 'date'],
            'socio.data_ocupacao' => ['nullable', 'date'],
            'socio.telefone_contato' => ['nullable', 'string', 'max:45'],
            'socio.n_moradores_declarado' => ['nullable', 'integer', 'min:0', 'max:100'],
            'socio.principal_fonte_renda' => ['nullable', 'string', 'max:2000'],
            'socio.qtd_contribuintes' => ['nullable', 'integer', 'min:0', 'max:100'],
            'socio.beneficios_sociais' => ['nullable', 'string', 'max:2000'],
            'socio.proprietario_nome' => ['nullable', 'string', 'max:255'],
            'socio.proprietario_endereco' => ['nullable', 'string', 'max:255'],
            'socio.proprietario_telefone' => ['nullable', 'string', 'max:45'],
            'socio.area_livre' => ['nullable', 'string', 'max:255'],
            'socio.observacoes_imovel' => ['nullable', 'string', 'max:5000'],
            'socio.tempo_residencia_texto' => ['nullable', 'string', 'max:150'],
            'socio.forma_aquisicao' => ['nullable', 'string', 'max:2000'],
            'socio.situacao_legal_obs' => ['nullable', 'string', 'max:2000'],
            'socio.proprietario_anterior_nome' => ['nullable', 'string', 'max:255'],
            'socio.proprietario_anterior_doc' => ['nullable', 'string', 'max:30'],
            'socio.como_ocupou' => ['nullable', 'string', 'max:2000'],
            'socio.iptu_desde' => ['nullable', 'integer', 'digits:4', 'min:1900', 'max:2100'],
            'socio.num_comodos' => ['nullable', 'integer', 'min:0', 'max:200'],
            'socio.num_quartos' => ['nullable', 'integer', 'min:0', 'max:100'],
            'socio.num_pavimentos' => ['nullable', 'integer', 'min:0', 'max:50'],
            'socio.banheiro_dentro' => ['nullable', 'integer', 'min:0', 'max:50'],
            'socio.banheiro_fora' => ['nullable', 'integer', 'min:0', 'max:50'],
            'socio.banheiro_compartilha' => ['nullable', 'boolean'],
        ];

        $rules['socio.condicao_casa'] = $in('condicao_casa_opcoes');
        $rules['socio.posicao_entrevistado'] = $in('posicao_entrevistado_opcoes');
        $rules['socio.renda_formal_informal'] = $in('renda_formal_informal_opcoes');
        $rules['socio.renda_familiar_faixa'] = ['nullable', 'string', Rule::in($rendaFam)];
        $rules['socio.gastos_mensais_faixa'] = $in('gastos_mensais_faixa_opcoes');
        $rules['socio.uso_imovel'] = $in('uso_imovel_socio_opcoes');
        $rules['socio.situacao_posse'] = $in('situacao_posse_opcoes');
        $rules['socio.material_predominante'] = $in('material_predominante_opcoes');
        $rules['socio.condicao_edificacao'] = $in('condicao_edificacao_opcoes');
        $rules['socio.area_externa'] = $in('area_externa_opcoes');
        $rules['socio.tipologia'] = $in('tipologia_opcoes');
        $rules['socio.tipo_implantacao'] = $in('tipo_implantacao_opcoes');
        $rules['socio.posicao_lote'] = $in('posicao_lote_opcoes');
        $rules['socio.acesso_imovel'] = $in('acesso_imovel_opcoes');
        $rules['socio.entrada_para'] = $in('entrada_para_opcoes');
        $rules['socio.abastecimento_agua'] = $in('infra_sim_nao_redes_opcoes');
        $rules['socio.energia_eletrica'] = $in('infra_energia_opcoes');
        $rules['socio.esgoto'] = $in('infra_esgoto_opcoes');
        $rules['socio.coleta_lixo'] = $in('infra_lixo_opcoes');
        $rules['socio.pavimentacao'] = $in('infra_pavimentacao_opcoes');
        $rules['socio.situacao_terreno'] = $in('situacao_terreno_opcoes');
        $rules['socio.posse_area'] = $in('posse_area_opcoes');
        $rules['socio.houve_compra_venda'] = $in('sim_nao_curto_opcoes');
        $rules['socio.escritura'] = $in('escritura_opcoes');
        $rules['socio.contrato_promessa'] = $in('sim_nao_curto_opcoes');
        $rules['socio.documento_quitado'] = $in('sim_nao_curto_opcoes');
        $rules['socio.sabe_local_vendedor'] = $in('sim_nao_curto_opcoes');
        $rules['socio.paga_iptu'] = $in('sim_nao_curto_opcoes');

        return $rules;
    }
}
