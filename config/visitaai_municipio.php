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

];
