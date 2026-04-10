<?php

/**
 * Terminologia alinhada às recomendações do Ministério da Saúde (MS).
 * Uso: config('ms_terminologia.perfis'), config('ms_terminologia.atividades_pncd'), etc.
 *
 * Referências principais:
 * - Lei nº 11.350/2006 (ACE e ACS)
 * - Diretriz Nacional para Atuação Integrada dos ACE e ACS no Território (MS)
 * - Diretrizes Nacionais para Prevenção e Controle das Arboviroses Urbanas: Vigilância Entomológica e Controle Vetorial (MS)
 * - PNCD (Programa Nacional de Controle da Dengue), LIRAa/LIA (gov.br/saude/arboviroses/liraa)
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Nome do sistema e escopo (MS)
    |--------------------------------------------------------------------------
    */
    'sistema' => [
        /*
         * Escopo do produto: visitas de campo (inclui PNCD / vetores quando o município usa) +
         * cadastro municipal de imóveis e dados complementares (ocupantes, perfil socioeconômico), que não
         * dependem do uso entomológico.
         */
        'nome_subtitulo' => 'Imóveis, visitas de campo e cadastro complementar do imóvel, com apoio à vigilância entomológica e controle vetorial (PNCD/MS) quando aplicável',
        'nota_ms' => 'O módulo de visitas pode seguir as diretrizes do Ministério da Saúde (vigilância em saúde, arboviroses, PNCD). O cadastro de imóveis e dados complementares (ocupantes, informações socioeconômicas) é autônomo: apoia gestão e políticas municipais e pode ser usado com ou sem registro de visita vetorial.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Perfis de usuário (Lei 11.350/2006; Diretriz MS ACE/ACS)
    |--------------------------------------------------------------------------
    */
    'perfis' => [
        'gestor' => [
            'label_curto' => 'Gestor Municipal',
            'label_oficial' => 'Gestor municipal (coordenação da vigilância em saúde)',
            'nota_ms' => 'Responsável pela coordenação/supervisão das ações de vigilância no âmbito municipal (Diretriz MS, gestão da Vigilância em Saúde).',
        ],
        'agente_endemias' => [
            'label_curto' => 'Agente de Combate às Endemias',
            'label_abrev' => 'ACE',
            'sigla' => 'ACE',
            'nota_ms' => 'Lei nº 11.350/2006. Profissional do SUS com atribuições de vigilância, prevenção e controle de doenças e promoção da saúde (vigilância epidemiológica e ambiental).',
        ],
        'agente_saude' => [
            'label_curto' => 'Agente Comunitário de Saúde',
            'label_abrev' => 'ACS',
            'sigla' => 'ACS',
            'nota_ms' => 'Lei nº 11.350/2006. Atua na atenção básica e na Estratégia Saúde da Família; pode participar de ações integradas no território, inclusive LIRAa (Diretriz Nacional para Atuação Integrada dos ACE e ACS).',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Atividades PNCD (Programa Nacional de Controle da Dengue)
    | Diretrizes Nacionais - Vigilância Entomológica e Controle Vetorial (MS)
    |--------------------------------------------------------------------------
    */
    'atividades_pncd' => [
        '1' => [
            'codigo' => '1-LI',
            'label' => 'LI',
            'nome' => 'LI (Levantamento de Índice)',
            'nota_ms' => 'Atividade de rotina do PNCD. Vigilância entomológica.',
        ],
        '2' => [
            'codigo' => '2-LI+T',
            'label' => 'LI+T',
            'nome' => 'LI+T (Levantamento de Índice + Tratamento)',
            'nota_ms' => 'PNCD. Levantamento com tratamento focal/perifocal.',
        ],
        '3' => [
            'codigo' => '3-PPE+T',
            'label' => 'PPE+T',
            'nome' => 'PPE+T (Ponto Estratégico + Tratamento)',
            'nota_ms' => 'PNCD. Ponto estratégico com tratamento.',
        ],
        '4' => [
            'codigo' => '4-T',
            'label' => 'T',
            'nome' => 'T (Tratamento)',
            'nota_ms' => 'PNCD. Tratamento focal ou perifocal.',
        ],
        '5' => [
            'codigo' => '5-DF',
            'label' => 'DF',
            'nome' => 'DF (Delimitação de Foco)',
            'nota_ms' => 'PNCD. Delimitação de foco do vetor.',
        ],
        '6' => [
            'codigo' => '6-PVE',
            'label' => 'PVE',
            'nome' => 'PVE (Pesquisa Vetorial Especial)',
            'nota_ms' => 'PNCD. Pesquisa vetorial especial.',
        ],
        '7' => [
            'codigo' => '7-LIRAa',
            'label' => 'LIRAa',
            'nome' => 'LIRAa (Levantamento de Índice Rápido para Aedes aegypti)',
            'nota_ms' => 'Método simplificado de vigilância entomológica (MS). Pode ser realizado por ACE e ACS de forma integrada (Diretriz Nacional ACE/ACS).',
        ],
        '8' => [
            'codigo' => '8-PE',
            'label' => 'PE',
            'nome' => 'PE (Ponto Estratégico)',
            'nota_ms' => 'PNCD. Inspeção em ponto estratégico.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de visita (N/R) - PNCD
    |--------------------------------------------------------------------------
    */
    'visita_tipo' => [
        'N' => ['label' => 'Normal', 'nota_ms' => 'Visita de rotina.'],
        'R' => ['label' => 'Recuperação', 'nota_ms' => 'Visita de recuperação (revisita).'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Referências para rodapé / documentação
    |--------------------------------------------------------------------------
    */
    'referencias' => [
        'lei_11350' => 'Lei nº 11.350, de 5 de outubro de 2006 (atribuições ACE e ACS).',
        'diretriz_ace_acs' => 'Diretriz Nacional para Atuação Integrada dos Agentes de Combate às Endemias e Agentes Comunitários de Saúde no Território (MS).',
        'diretriz_arboviroses' => 'Diretrizes Nacionais para Prevenção e Controle das Arboviroses Urbanas: Vigilância Entomológica e Controle Vetorial (MS).',
        'pncd' => 'PNCD: Programa Nacional de Controle da Dengue. Ministério da Saúde.',
        'liraa' => 'LIRAa/LIA: Levantamento Rápido de Índices para Aedes aegypti. gov.br/saude/arboviroses/liraa',
    ],

    /*
    |--------------------------------------------------------------------------
    | Referências estaduais (RS)
    |--------------------------------------------------------------------------
    */
    'estado_rs' => [
        'ses' => 'SES-RS: Secretaria Estadual da Saúde do Rio Grande do Sul.',
        'cevs' => 'CEVS: Centro Estadual de Vigilância em Saúde (RS).',
        'nota' => 'No âmbito do RS, as ações devem observar as orientações da SES-RS e do CEVS, em consonância com as diretrizes nacionais do MS.',
    ],
];
