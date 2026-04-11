# Visita Aí - Arquitetura de Produto e Mensagem

## Objetivo do Produto
Visita Aí e uma plataforma municipal para organizar operacao territorial e transformar dados de campo em indicadores para decisao, gestao e transparencia.

## Estado atual da solucao (abril/2026)
- `application` (Laravel): sistema principal da operacao municipal.
- `website` (Nuxt): landing institucional e comercial.
- Os dois repositorios partilham a mesma mensagem de produto, mas possuem ciclos de deploy independentes.

O produto nao e um sistema de nicho unico. A vigilancia em saude e uma especializacao importante, mas o nucleo da plataforma e mais amplo.

## Arquitetura de Escopo

### 1) Nucleo municipal (sempre ativo)
- Cadastro territorial de locais/imoveis
- Operacao de campo (visitas e pendencias)
- Paineis e indicadores para gestao
- Relatorios e exportacoes
- Consulta publica por codigo/QR sem dados sensiveis
- Governanca de acesso por perfil e auditoria

### 2) Especializacoes em saude (quando contratadas)
- Vigilancia entomologica
- LIRAa
- PNCD
- Cadastro e leitura de doencas/agravos monitorados

### 3) Modulos complementares (opcionais)
- Cadastro de ocupantes/socioeconomico
- Indicadores agregados complementares para planejamento local

## Proposta de Valor
- Para gestor: indicadores acionaveis, leitura de territorio e prestacao de contas
- Para coordenacao tecnica: rotina padronizada, rastreabilidade e continuidade
- Para equipe de campo: captura simples, fluxo direto e sincronizacao
- Para municipio/cidadao: transparencia operacional com consulta publica segura

## Linguagem Oficial Recomendada
Usar sempre:
- "Nucleo municipal"
- "Especializacoes em saude"
- "Modulos complementares"

Evitar:
- Definir o produto apenas como "sistema de dengue"
- Misturar objetivo principal com modulo opcional

## Regra de Comunicacao
Em qualquer tela, proposta comercial ou material institucional, responder em ordem:
1. Qual problema municipal resolvemos?
2. Qual o nucleo obrigatorio da plataforma?
3. Quais especializacoes e opcionais podem ser ativados?

## Criterios de Tela (UX e Conteudo)
Cada tela deve ter:
- Titulo orientado a beneficio
- Subtitulo curto com contexto
- No maximo uma acao primaria com destaque
- Linguagem objetiva, sem excesso de texto tecnico
- Consistencia de nomenclatura com este documento

## Checklist de Revisao de Copia
- Esta claro o objetivo da tela em uma frase?
- O texto evita redundancia e jargao desnecessario?
- O usuario entende se o item e nucleo, especializacao ou opcional?
- O texto ajuda decisao ou apenas ocupa espaco?

## Observacao de Conformidade
Quando houver uso de modulo socioeconomico ou ocupantes, manter a comunicacao de que nao substitui os sistemas nacionais obrigatorios (como e-SUS/PEC quando aplicavel).