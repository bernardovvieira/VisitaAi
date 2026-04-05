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

    /*
    |--------------------------------------------------------------------------
    | Proteção de dados: legislação federal brasileira
    |--------------------------------------------------------------------------
    | CF/1988, Lei 8.080/1990, LGPD, LAI, Marco Civil e normas da ANPD.
    | Complementar com assessoria jurídica municipal e Encarregado (art. 41 LGPD).
    */
    'lgpd' => [
        'titulo' => 'Proteção de dados pessoais: legislação federal (LGPD, LAI, Marco Civil e correlatos)',

        'quadro_legislacao_federal' => 'Observa-se, entre outras, a Constituição Federal de 1988: inviolabilidade da intimidade e da vida privada (art. 5º, X e XII); direito à saúde e organização do SUS (arts. 196 a 200). Lei nº 8.080/1990: promoção, proteção e recuperação da saúde e organização do SUS (ações de vigilância sanitária articuladas no sistema). Lei nº 13.709/2018 (LGPD). Lei nº 12.527/2011 (LAI). Lei nº 12.965/2014 (Marco Civil da Internet, arts. 7º a 10, inclusive registros e sigilo). Normas editadas pela ANPD (institucionalizada pela Lei nº 14.058/2020, nos termos do art. 55-J da LGPD), quando aplicáveis.',

        'principios_lgpd' => 'Princípios do art. 6º da LGPD: boa-fé; finalidade; adequação; necessidade; livre acesso; qualidade dos dados; transparência; segurança; prevenção; não discriminação; responsabilização e prestação de contas.',

        'resumo_sistema' => 'O Visita Aí trata dados pessoais para vigilância entomológica, controle vetorial e gestão de saúde pública municipal, em consonância com a Constituição Federal, a Lei nº 8.080/1990 e a LGPD, com minimização, transparência e governança nos termos dos arts. 6º e 23 a 27 da LGPD.',

        'controlador' => 'O órgão ou ente municipal competente atua como controlador (art. 5º, VI, LGPD). Servidores e demais pessoas autorizadas observam os arts. 23, caput, e 24 da LGPD ao tratar dados em nome do controlador.',

        'finalidades' => 'Finalidades: registro de visitas de campo; cadastro de imóveis e dados operacionais da vigilância; indicadores agregados para planejamento; dados complementares de ocupantes quando limitados às finalidades de saúde pública e vigilância (art. 6º, II e III, LGPD), em articulação com a Lei nº 8.080/1990.',

        'bases_legais' => 'Tratamento amparado no art. 7º da LGPD, conforme o caso e a análise jurídica: inciso II (obrigação legal ou regulamentar); III (executar políticas públicas previstas em leis ou regulamentos, inclusive uso compartilhado quando autorizado); IV (estudos por órgão de pesquisa, observadas anonimização ou sigilo quando necessário); VII (proteção da vida ou da incolumidade física); I (consentimento), para dados facultativos. Dados sensíveis: art. 11 e § 4º do art. 7º. Setor público: arts. 23 a 27 da LGPD.',

        'categorias_dados' => 'Podem ser tratados: dados de identificação profissional e institucional de agentes; dados do imóvel e de localização; registros de visitas e pendências operacionais; agregados para painéis; e, se informados, dados de ocupantes (idade quando houver data de nascimento; informações socioeconômicas; origem racial ou étnica, hipótese de dado sensível no art. 5º, II, LGPD).',

        'dados_sensiveis_medidas' => 'Dados sensíveis observam o art. 11 da LGPD e medidas dos arts. 46 a 49 (segurança); acesso restrito; finalidade legítima; vedado uso discriminatório (art. 6º, IX).',

        'operadores' => 'Terceiros que tratam dados em nome do Poder Público municipal são operadores (art. 5º, VII, LGPD), com deveres do art. 41 e instrumentos jurídicos aplicáveis ao setor público.',

        'titulares_direitos' => 'Direitos do art. 18 da LGPD perante o controlador, sem prejuízo de hipóteses legais de indeferimento, incluindo confirmação de tratamento, acesso, correção, anonimização, bloqueio ou eliminação, portabilidade nos termos regulamentares da ANPD, informações sobre compartilhamento e sobre consentimento, revogação do consentimento quando for a base legal, e demais prerrogativas previstas na legislação federal.',

        'encarregado_titulo' => 'Encarregado (DPO) / canal municipal (art. 41 da LGPD)',
        'encarregado_texto' => 'O art. 41 da LGPD prevê o encarregado como canal de comunicação entre controlador, titulares e ANPD. O município deve divulgar contato funcional (e-mail, protocolo ou ouvidoria) em sítio oficial, conforme arts. 23, § 1º, e 26 da LGPD quando aplicáveis ao setor público.',

        'retencao' => 'Conservação conforme finalidade, necessidade e prazos legais ou regulamentares de guarda; possibilidade de anonimização ou eliminação quando autorizada (art. 16, LGPD) e legislação de arquivo público.',

        'seguranca' => 'Medidas de segurança e governança nos arts. 46 a 49 da LGPD, proporcionais ao risco; podem observar resoluções e guias da ANPD (ex.: orientações sobre boas práticas e segurança). Controles técnicos: autenticação, perfis, minimização em consultas públicas, redução de risco em agregados.',

        'compartilhamento' => 'Vedada a comercialização de dados (art. 7º, § 1º, II, LGPD). Uso compartilhado pela administração pública observa o art. 26 da LGPD e regulamentação específica.',

        'transf_internacional' => 'Transferência internacional, se houver, conforme arts. 33 a 36 da LGPD, decisões de adequação, cláusulas-padrão e demais mecanismos previstos em lei e regulamento da ANPD.',

        'cookies_tecnologia' => 'Registros e aplicações de internet podem observar os arts. 7º a 10 da Lei nº 12.965/2014 (Marco Civil), inclusive guarda e confidencialidade; cookies ou armazenamento local necessários à sessão e segurança alinham-se à LGPD.',

        'lei_acesso_informacao' => 'Acesso a informações do Poder Público: Lei nº 12.527/2011 (LAI), arts. 3º a 6º (princípios), observados limites sobre dados pessoais e informações classificadas (arts. 7º a 31), em harmonização com a LGPD e com o art. 5º, XXXIII e XLI, da Constituição Federal.',

        'autoridade_nacional' => 'A Autoridade Nacional de Proteção de Dados (ANPD) exerce competências normativas e fiscalizatórias previstas nos arts. 55-J e seguintes da LGPD e na Lei nº 14.058/2020.',

        'atualizacao' => 'Texto informativo conforme legislação federal vigente; não dispensa ROPA, RIPD quando obrigatório, política municipal nem atualização junto ao Planalto/ANPD. Revisar com assessoria jurídica.',

        'contextos' => [
            'ocupantes_cadastro' => 'Minimização e finalidade (art. 6º, LGPD) e competências de vigilância em saúde (Lei nº 8.080/1990); evitar dados clínicos de prontuário (fluxos do SUS em sistemas federais).',
            'visitas_observacoes_ocupantes' => 'Registro objetivo e não discriminatório; proteção da intimidade (CF, art. 5º, X e XII) e regime de dados sensíveis (LGPD, art. 11), quando houver.',
            'painel_indicadores' => 'Acesso restrito; agregados com redução de risco na tela; exportação CSV com dados institucionais detalhados; uso interno, sem publicidade irresponsável (LGPD e LAI).',
            'export_csv' => 'Documento para administração pública municipal; armazenamento e tráfego conforme políticas de TI; vedação de divulgação aberta sem critério jurídico (arts. 7º e 18, LGPD; LAI).',
            'painel_gestor_sensivel' => 'Acesso apenas por perfil autorizado; desvio de finalidade ou vazamento pode ensejar sanções previstas no ordenamento federal e municipal (LGPD e legislação de improbidade/funcional, quando aplicável).',
            'consulta_publica' => 'Transparência e minimização (arts. 6º, III, LGPD; arts. 3º a 6º, LAI); sem exposição de dados sensíveis ou identificação de moradores.',
        ],

        'csv_secao_titulo' => 'AVISO_LEGISLACAO_FEDERAL_EXPORTACAO',
        'csv_aviso_linhas' => [
            'Documento gerado no Visita Aí. Controlador: administração pública municipal competente.',
            'Uso restrito à gestão e ao planejamento em vigilância/saúde pública: Constituição Federal (arts. 196-200), Lei nº 8.080/1990 e LGPD (Lei nº 13.709/2018).',
            'Matriz escolaridade × renda com contagens completas: dado institucional sensível; não publicar em meios abertos sem anonimização e parecer jurídico.',
            'Segurança e compartilhamento: arts. 6º, 7º, 11, 18, 23 a 27, 37, 41 e 46 a 49 da LGPD; transparência e sigilo: Lei nº 12.527/2011.',
            'Direitos dos titulares: art. 18 da LGPD junto ao encarregado/canal municipal e à ANPD nos casos cabíveis.',
        ],
    ],

    'ocupantes' => [
        'titulo_secao_local' => 'Ocupantes do imóvel',
        'titulo_listagem' => 'Ocupantes neste imóvel',
        'botao_gerenciar' => 'Gerenciar ocupantes',
        'disclaimer' => 'Dados operacionais vinculados a este imóvel. Tratamento conforme legislação federal (Constituição Federal; Lei nº 8.080/1990, SUS; Lei nº 13.709/2018, LGPD) e normas municipais. Informe apenas o necessário (princípio da minimização, art. 6º, III, LGPD).',
        'painel_gestor_titulo' => 'Ocupantes registrados',
        'painel_gestor_subtitulo' => '',
        'painel_gestor_bairros' => 'Por bairro',
        'painel_sensivel_gestor_titulo' => 'Dados dos ocupantes (acesso gestor municipal)',
        'painel_sensivel_gestor_texto' => 'Informações identificáveis e socioeconômicas deste imóvel após busca em Locais. Acesso restrito a gestores, para finalidades legítimas de gestão e vigilância em saúde pública (CF, arts. 196-200; Lei nº 8.080/1990; LGPD).',
        'visitas_ocupantes_titulo' => 'Registros nas visitas sobre ocupantes',
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
        'ate_meio_salario' => 'Até 1/2 salário mínimo',
        'ate_1_sm' => 'Até 1 salário mínimo',
        'ate_2_sm' => 'De 1 a 2 salários mínimos',
        'ate_3_sm' => 'De 2 a 3 salários mínimos',
        'acima_3_sm' => 'De 3 a 4 salários mínimos',
        'acima_5_sm' => 'Acima de 5 salários mínimos',
    ],

    /*
    | Referência próxima à classificação étnico-racial (IBGE). Uso operacional municipal no Visita Aí.
    */
    'cor_raca_opcoes' => [
        'nao_informado' => 'Não informado',
        'branca' => 'Branca',
        'preta' => 'Preta',
        'parda' => 'Parda',
        'amarela' => 'Amarela',
        'indigena' => 'Indígena',
    ],

    'situacao_trabalho_opcoes' => [
        'nao_informado' => 'Não informado',
        'empregado' => 'Empregado(a) (com carteira ou outro vínculo)',
        'autonomo' => 'Autônomo(a) / por conta própria',
        'desempregado' => 'Desempregado(a)',
        'aposentado' => 'Aposentado(a) ou pensionista',
        'dona_casa' => 'Dona(o) de casa',
        'estudante' => 'Somente estudante',
        'outro' => 'Outra situação',
    ],

    /*
    |--------------------------------------------------------------------------
    | Indicadores agregados (gestor): apenas dados registrados no Visita Aí
    |--------------------------------------------------------------------------
    | Textos operacionais e públicos; sem finalidade de substituir cadastros oficiais.
    */
    'indicadores' => [
        'minimo_registros_bairro' => 5,
        'minimo_celula_cruzamento' => 5,
        'menu' => 'Indicadores',
        'titulo_pagina' => 'Indicadores',
        'subtitulo' => 'Resumo dos ocupantes cadastrados no Visita Aí: bairro do imóvel, faixa etária, escolaridade, renda, cor/raça e situação de trabalho.',
        'subtitulo_detalhe' => 'Agregados com minimização e boas práticas de privacidade (art. 6º da LGPD). Exportação CSV restrita ao gestor; uso institucional com aviso de legislação federal (LGPD, LAI) no arquivo e nesta página.',
        'aviso' => 'Panorama a partir dos registros internos. Não substitui relatórios oficiais, e-SUS ou obrigações de informação à União, estados ou distrito federal quando aplicáveis.',
        'aviso_privacidade' => 'Supressão de totais em bairros ou células com poucos registros reduz risco de reidentificação (art. 6º da LGPD; orientações gerais da ANPD sobre governança em dados). O CSV exportado contém detalhamento estatístico: classificar como informação para gestão interna, nos termos da LAI e da LGPD.',
        'texto_celula_suprimida' => '-',
        'titulo_secao_bairro' => 'Por bairro do imóvel',
        'titulo_secao_faixa_global' => 'Faixa etária',
        'legenda_mapa_calor_faixa' => 'Cores mais quentes indicam faixas com mais ocupantes, em escala relativa à faixa mais numerosa. A porcentagem é a participação no total geral.',
        'legenda_mapa_calor_tabela' => 'Nas colunas de idade, o fundo segue a mesma lógica por coluna: compara os bairros entre si em cada faixa.',
        'titulo_secao_escolaridade' => 'Escolaridade',
        'titulo_secao_renda' => 'Renda',
        'titulo_secao_completude' => 'Qualidade do preenchimento',
        'subtitulo_completude' => 'Percentual de ocupantes com data de nascimento informada e com escolaridade, renda, cor/raça ou situação de trabalho além da opção “Não informado”.',
        'titulo_secao_cruzamento' => 'Escolaridade e renda',
        'legenda_cruzamento' => 'Cada célula é a quantidade de ocupantes na combinação de escolaridade (linha) e faixa de renda (coluna). Células com poucos casos são ocultas pelo mesmo critério de privacidade das tabelas por bairro.',
        'titulo_secao_cor_raca' => 'Cor ou raça (autodeclarada)',
        'titulo_secao_situacao_trabalho' => 'Situação no trabalho',
        'botao_export_csv' => 'Exportar CSV',
        'export_csv_aviso' => 'Arquivo exclusivo para uso interno da administração pública municipal. Contém matriz escolaridade × renda sem supressão por célula (dado institucional sensível). Não trafegar por canais não oficiais. Fundamentos: Constituição Federal; Lei nº 8.080/1990; Lei nº 13.709/2018 (arts. 6º, 7º, 18, 46 a 49); Lei nº 12.527/2011 quando couber. O CSV inclui seção de aviso legal federal.',
        'sem_bairro_label' => 'Sem bairro',
        'colunas_faixas' => [
            '0-11' => '0 a 11 anos',
            '12-17' => '12 a 17 anos',
            '18-59' => '18 a 59 anos',
            '60+' => '60 anos ou mais',
            'sem_info' => 'Sem data de nascimento',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Página inicial pública (/) — indicadores agregados
    |--------------------------------------------------------------------------
    | Ajuste via .env: VISITAI_WELCOME_PUBLIC_INDICADORES, etc.
    */
    'welcome_public' => [
        /** Exibe o bloco numérico na home. Desligue para esconder totais até o go-live municipal. */
        'indicadores_habilitados' => filter_var(
            env('VISITAI_WELCOME_PUBLIC_INDICADORES', 'true'),
            FILTER_VALIDATE_BOOL
        ),

        /**
         * Só mostra a contagem de bairros com visita quando houver pelo menos N visitas
         * (reduz risco de reidentificação; alinhe ao minimo_registros_bairro se desejar).
         */
        'min_visitas_para_exibir_bairros' => (int) env('VISITAI_WELCOME_MIN_VISITAS_BAIRROS', 5),

        /** Se true, valores zero aparecem como "—" em vez de 0 (visual mais limpo no pré-cadastro). */
        'ocultar_valores_zero' => filter_var(
            env('VISITAI_WELCOME_OCULTAR_ZEROS', 'false'),
            FILTER_VALIDATE_BOOL
        ),
    ],

];
