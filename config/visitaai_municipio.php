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
        'titulo_secao_local' => 'Ocupantes do imóvel (Visita Aí)',
        'titulo_listagem' => 'Ocupantes neste imóvel',
        'botao_gerenciar' => 'Gerenciar ocupantes',
        'disclaimer' => 'Registro operacional municipal no Visita Aí. Não substitui o cadastro de cidadãos no e-SUS APS, o PEC nem a Ficha de Visita Domiciliar e Territorial.',
        'painel_gestor_titulo' => 'Ocupantes (Visita Aí)',
        'painel_gestor_subtitulo' => 'Registros neste sistema — complementar ao e-SUS e ao SisPNCD.',
        'painel_gestor_bairros' => 'Distribuição por bairro (painel local)',
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
    | Indicadores agregados (gestor) — apenas dados registrados no Visita Aí
    |--------------------------------------------------------------------------
    | Textos operacionais e públicos; sem finalidade de substituir cadastros oficiais.
    */
    'indicadores' => [
        'minimo_registros_bairro' => 5,
        'menu' => 'Indicadores municipais',
        'titulo_pagina' => 'Indicadores municipais (ocupantes no Visita Aí)',
        'subtitulo' => 'Agregados por bairro do imóvel, faixa etária, escolaridade e faixa de renda informada — somente registros feitos neste sistema.',
        'aviso' => 'Informação complementar à operação de vigilância em saúde ambiental. Não substitui indicadores oficiais do SUS, cadastros e-SUS/PEC nem outras bases municipais obrigatórias.',
        'aviso_privacidade' => 'Para bairros com poucos registros, os números são ocultados para reduzir risco de identificação.',
        'texto_celula_suprimida' => '—',
        'titulo_secao_bairro' => 'Por bairro do imóvel',
        'titulo_secao_faixa_global' => 'Faixa etária (todos os registros)',
        'titulo_secao_escolaridade' => 'Escolaridade informada (agregado)',
        'titulo_secao_renda' => 'Renda informada (agregado)',
        'sem_bairro_label' => '(Sem bairro informado)',
        'colunas_faixas' => [
            '0-11' => '0–11 anos',
            '12-17' => '12–17 anos',
            '18-59' => '18–59 anos',
            '60+' => '60 anos ou mais',
            'sem_info' => 'Sem data de nascimento',
        ],
    ],

];
