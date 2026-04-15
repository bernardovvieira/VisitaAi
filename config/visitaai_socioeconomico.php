<?php

/**
 * Opções da ficha socioeconômica municipal (imóvel + domicílio), alinhadas ao modelo em papel.
 * Chaves estáveis para validação e armazenamento; rótulos para UI e PDF.
 */
return [

    'condicao_casa_opcoes' => [
        'nao_informado' => 'Não informado',
        'alugada' => 'Alugada',
        'propria' => 'Própria',
        'cedida' => 'Cedida',
        'sub_alugada' => 'Subalugada',
        'emprestada' => 'Emprestada',
        'comodato' => 'Comodato',
        'outros' => 'Outros',
    ],

    'posicao_entrevistado_opcoes' => [
        'nao_informado' => 'Não informado',
        'titular' => 'Titular',
        'conjuge_companheiro' => 'Cônjuge ou companheiro(a)',
        'inquilino' => 'Inquilino(a)',
        'morador' => 'Morador(a)',
    ],

    'renda_formal_informal_opcoes' => [
        'nao_informado' => 'Não informado',
        'formal' => 'Formal',
        'informal' => 'Informal',
        'misto' => 'Misto',
    ],

    'uso_imovel_socio_opcoes' => [
        'nao_informado' => 'Não informado',
        'residencial' => 'Residencial',
        'comercial' => 'Comercial',
        'misto' => 'Misto',
        'area_rural' => 'Área rural',
        'institucional' => 'Institucional',
        'outros' => 'Outros',
    ],

    'situacao_posse_opcoes' => [
        'nao_informado' => 'Não informado',
        'propria_quitada' => 'Própria (quitada)',
        'propria_financiada' => 'Própria (financiada)',
        'alugada' => 'Alugada',
        'cedida' => 'Cedida',
    ],

    'material_predominante_opcoes' => [
        'nao_informado' => 'Não informado',
        'alvenaria' => 'Alvenaria',
        'madeira' => 'Madeira',
        'mista' => 'Mista',
        'outros' => 'Outros',
    ],

    'condicao_edificacao_opcoes' => [
        'nao_informado' => 'Não informado',
        'muito_bom' => 'Muito bom',
        'bom' => 'Bom',
        'razoavel' => 'Razoável',
        'ruim' => 'Ruim',
    ],

    'area_externa_opcoes' => [
        'nao_informado' => 'Não informado',
        'sim' => 'Sim',
        'nao' => 'Não',
        'parcial' => 'Parcial',
    ],

    'tipologia_opcoes' => [
        'nao_informado' => 'Não informado',
        'casa_sobrado' => 'Casa / sobrado',
        'apartamento' => 'Apartamento',
        'outros' => 'Outros',
    ],

    'tipo_implantacao_opcoes' => [
        'nao_informado' => 'Não informado',
        'isolado' => 'Isolado',
        'conjunto' => 'Conjunto / vilas',
        'apartamento' => 'Apartamento em edifício',
    ],

    'posicao_lote_opcoes' => [
        'nao_informado' => 'Não informado',
        'frente' => 'Frente',
        'fundos' => 'Fundos',
        'meio' => 'Meio de quadra',
    ],

    'acesso_imovel_opcoes' => [
        'nao_informado' => 'Não informado',
        'individual' => 'Individual / particular',
        'comum' => 'Comum a mais de um morador',
    ],

    'entrada_para_opcoes' => [
        'nao_informado' => 'Não informado',
        'rua_principal' => 'Rua principal',
        'travessa' => 'Travessa',
        'beco' => 'Beco',
        'rua_sem_saida' => 'Rua sem saída',
        'outros' => 'Outros',
    ],

    'infra_sim_nao_redes_opcoes' => [
        'nao_informado' => 'Não informado',
        'rede_publica' => 'Rede pública',
        'poco' => 'Poço / cisterna',
        'carro_pipa' => 'Carro-pipa / outro',
        'nao_tem' => 'Não tem',
    ],

    'infra_energia_opcoes' => [
        'nao_informado' => 'Não informado',
        'rede_publica' => 'Rede pública',
        'gerador' => 'Gerador / solar / outro',
        'nao_tem' => 'Não tem',
    ],

    'infra_esgoto_opcoes' => [
        'nao_informado' => 'Não informado',
        'rede' => 'Rede coletora',
        'fossa' => 'Fossa',
        'ceu_aberto' => 'Céu aberto / outro',
        'nao_tem' => 'Não tem',
    ],

    'infra_lixo_opcoes' => [
        'nao_informado' => 'Não informado',
        'coleta_publica' => 'Coleta pública',
        'queima_enterra' => 'Queima / enterra / outro',
        'nao_tem' => 'Não tem serviço',
    ],

    'infra_pavimentacao_opcoes' => [
        'nao_informado' => 'Não informado',
        'asfalto' => 'Asfalto',
        'paralelepipedo' => 'Paralelepípedo / blocos',
        'terra' => 'Terra / não pavimentada',
    ],

    'situacao_terreno_opcoes' => [
        'nao_informado' => 'Não informado',
        'plano' => 'Plano',
        'aclive' => 'Aclive / declive',
        'alagavel' => 'Sujeito a alagamento',
        'outros' => 'Outros',
    ],

    'posse_area_opcoes' => [
        'nao_informado' => 'Não informado',
        'propria' => 'Área própria',
        'publica' => 'Área pública / concessão',
        'ocupacao' => 'Ocupação / outra situação',
    ],

    'sim_nao_curto_opcoes' => [
        'nao_informado' => 'Não informado',
        'sim' => 'Sim',
        'nao' => 'Não',
    ],

    'gastos_mensais_faixa_opcoes' => [
        'nao_informado' => 'Não informado',
        'ate_500' => 'Até R$ 500',
        '501_1500' => 'R$ 501 a 1.500',
        '1501_3000' => 'R$ 1.501 a 3.000',
        '3001_6000' => 'R$ 3.001 a 6.000',
        'acima_6000' => 'Acima de R$ 6.000',
    ],

    'escritura_opcoes' => [
        'nao_informado' => 'Não informado',
        'registrada' => 'Registrada em cartório',
        'sem_escritura' => 'Sem escritura',
        'em_processo' => 'Em processo / outro',
    ],

    'sexo_opcoes' => [
        'nao_informado' => 'Não informado',
        'feminino' => 'Feminino',
        'masculino' => 'Masculino',
        'outro' => 'Outro',
        'prefiro_nao_informar' => 'Prefiro não informar',
    ],

    'estado_civil_opcoes' => [
        'nao_informado' => 'Não informado',
        'solteiro' => 'Solteiro(a)',
        'casado' => 'Casado(a)',
        'uniao_estavel' => 'União estável',
        'divorciado' => 'Divorciado(a)',
        'viuvo' => 'Viúvo(a)',
        'separado' => 'Separado(a)',
        'outros' => 'Outros',
    ],

    'parentesco_opcoes' => [
        'nao_informado' => 'Não informado',
        'titular' => 'Titular',
        'conjuge' => 'Cônjuge / companheiro(a)',
        'filho' => 'Filho(a)',
        'pai_mae' => 'Pai / mãe',
        'irmao' => 'Irmão(ã)',
        'neto' => 'Neto(a)',
        'outro_familiar' => 'Outro familiar',
        'nao_familiar' => 'Não familiar (ex.: inquilino)',
    ],

    'secao_titulos' => [
        'entrevista' => '1. Entrevista e domicílio',
        'economia' => '2. Economia do grupo familiar',
        'proprietario' => '3. Proprietário (se aluguel / cedido)',
        'imovel_caracteristicas' => '5. Características do imóvel e cadastro físico',
        'infraestrutura' => '6. Infraestrutura e serviços',
        'terreno' => '7. Terreno e uso',
        'historico' => '8. Histórico da posse',
        'finalizacao' => '9. Finalização',
        'moradores' => 'Composição familiar: cada morador',
    ],

    'disclaimer' => 'Ficha socioeconômica complementar ao cadastro territorial e às obrigações de sistemas oficiais (ex.: CadÚnico, e-SUS). Informe apenas o necessário (minimização, LGPD). CPF/RG são opcionais e sensíveis.',
];
