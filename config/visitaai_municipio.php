<?php

/**
 * Dados complementares municipalmente no Visita Aí (vigilância entomológica / PNCD).
 *
 * NÃO equivale a: e-SUS APS, PEC, Cadastro Territorial ou Ficha de Visita Domiciliar
 * e Territorial. Esses fluxos permanecem nos sistemas federais obrigatórios do município.
 *
 * @see docs/ESUS-SISPNCD-DIFERENCIACAO-E-MEDIDAS.md
 * @see docs/CONFORMIDADE-MS-FLUXO.md
 */
return [

    'ocupantes' => [
        'titulo_secao_local' => 'Ocupantes do imóvel',
        'titulo_listagem' => 'Ocupantes neste imóvel',
        'botao_gerenciar' => 'Gerenciar ocupantes',
        'disclaimer' => 'Dados operacionais vinculados a este imóvel no Visita Aí.',
        'painel_gestor_titulo' => 'Ocupantes registrados',
        'painel_gestor_subtitulo' => '',
        'painel_gestor_bairros' => 'Por bairro',
    ],

    'escolaridade_opcoes' => [
        'nao_informado' => 'Não informado',
        'fundamental_incompleto' => 'Fundamental incompleto',
        'fundamental_completo' => 'Fundamental completo',
        'medio_incompleto' => 'Médio incompleto',
        'medio_completo' => 'Médio completo',
        'superior_incompleto' => 'Superior incompleto',
        'superior_completo' => 'Superior completo',
    ],

    'renda_faixa_opcoes' => [
        'nao_informado' => 'Não informado',
        'ate_meio_salario' => 'Até meio salário mínimo',
        'ate_1_sm' => 'Até 1 salário mínimo',
        'ate_2_sm' => 'De 1 a 2 salários mínimos',
        'ate_3_sm' => 'De 2 a 3 salários mínimos',
        'acima_3_sm' => 'Acima de 3 salários mínimos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Indicadores agregados (gestor): apenas dados registrados no Visita Aí
    |--------------------------------------------------------------------------
    | Textos operacionais e públicos; sem finalidade de substituir cadastros oficiais.
    */
    'indicadores' => [
        'minimo_registros_bairro' => 5,
        'menu' => 'Indicadores',
        'titulo_pagina' => 'Indicadores',
        'subtitulo' => 'Resumo dos ocupantes cadastrados no Visita Aí: bairro do imóvel, faixa etária, escolaridade e renda.',
        'subtitulo_detalhe' => 'Escolaridade e renda aparecem no resumo somente quando foram informadas no cadastro.',
        'aviso' => 'Panorama a partir dos registros deste sistema. Não substitui relatórios oficiais ou outras bases de dados do município.',
        'aviso_privacidade' => 'Para bairros com poucos registros, os números são ocultados para reduzir risco de identificação.',
        'texto_celula_suprimida' => '-',
        'titulo_secao_bairro' => 'Por bairro do imóvel',
        'titulo_secao_faixa_global' => 'Faixa etária',
        'legenda_mapa_calor_faixa' => 'Cores mais quentes indicam faixas com mais ocupantes, em escala relativa à faixa mais numerosa. A porcentagem é a participação no total geral.',
        'legenda_mapa_calor_tabela' => 'Nas colunas de idade, o fundo segue a mesma lógica por coluna: compara os bairros entre si em cada faixa.',
        'titulo_secao_escolaridade' => 'Escolaridade',
        'titulo_secao_renda' => 'Renda',
        'sem_bairro_label' => 'Sem bairro',
        'colunas_faixas' => [
            '0-11' => '0 a 11 anos',
            '12-17' => '12 a 17 anos',
            '18-59' => '18 a 59 anos',
            '60+' => '60 anos ou mais',
            'sem_info' => 'Sem data de nascimento',
        ],
    ],

];
