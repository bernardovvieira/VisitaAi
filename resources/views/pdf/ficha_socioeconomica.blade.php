<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Ficha socioeconômica') }}: {{ $local->loc_codigo_unico }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #111; }
        h1 { font-size: 13pt; margin: 0 0 6px 0; text-align: center; }
        h2 { font-size: 10pt; margin: 0 0 6px 0; border-bottom: 1px solid #333; padding-bottom: 2px; }
        /* Use fixed table layout and force word wrapping so long values don't overflow the page */
        table { width: 100%; border-collapse: collapse; table-layout: fixed; word-wrap: break-word; overflow-wrap: break-word; }
        /* Use overflow-wrap and allow aggressive breaks to avoid overflow for very long strings */
        th, td { border: 1px solid #444; padding: 4px 6px; vertical-align: top; overflow-wrap: anywhere; word-wrap: break-word; white-space: normal; hyphens: auto; word-break: break-all; }
        .renda { font-weight: 700; color: #111; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; width: 30%; }
        .muted { color: #555; font-size: 8pt; }
        .small { font-size: 8pt; }
        .section { margin-bottom: 10px; }
        .panel { border: 1px solid #444; padding: 6px; margin-bottom: 10px; }
        .grid-2 { display: table; width: 100%; table-layout: fixed; border-collapse: collapse; }
        .grid-2 .cell { display: table-cell; width: 50%; vertical-align: top; padding: 0 4px; }
        .grid-3 { display: table; width: 100%; table-layout: fixed; border-collapse: collapse; }
        .grid-3 .cell { display: table-cell; width: 33.3333%; vertical-align: top; padding: 0 4px; }
        /* Compact tables use slightly smaller font and padding to fit more columns */
        .compact th, .compact td { padding: 3px 5px; font-size: 8pt; }
        /* Make the occupants table more compact to avoid horizontal overflow */
        .panel table tbody td { vertical-align: top; }
        .center { text-align: center; }

          /* Reserve space for header/footer and keep content separated from header
              Dompdf repeats fixed-position elements on each page when margins reserve space.
              Use a negative top for the fixed header so it sits in the page margin. */
          @page { margin: 100px 20px 70px 20px; }
          .header { position: fixed; top: -90px; left: 0; right: 0; height: 80px; padding: 10px 12px; font-family: DejaVu Sans, sans-serif; }
          .footer { position: fixed; bottom: -40px; left: 0; right: 0; height: 48px; padding: 6px 12px; font-family: DejaVu Sans, sans-serif; }
    </style>
</head>
<body>

<!-- Header (fixed) -->
<div class="header">
    <div style="position:relative; font-size:10pt;">
        <div style="text-align:center; font-size:11pt; font-weight:700; color:#111; border-bottom:1px solid #ccc; padding-bottom:6px;">CADASTRO SOCIOECONÔMICO</div>
        <div style="height:8px;"></div>
        <div style="text-align:right; font-size:9pt; color:rgba(0,0,0,0.45); font-weight:600; font-family: DejaVu Sans, sans-serif;"><span style="font-weight:600;">Imóvel #{{ $local->loc_codigo_unico }}</span></div>
    </div>
</div>
 
<!-- Document description: shown once, directly under the Imóvel code -->
<div style="box-sizing:border-box; padding:0 7px 8px 7px; font-size:8pt; color:#666; font-family: DejaVu Sans, sans-serif; line-height:1.2; text-align:justify; text-justify:inter-word;">
    <div style="width:100%; display:block;">
        Este cadastro reúne as informações coletadas na entrevista sobre o domicílio e seus ocupantes. Os dados pessoais incluídos neste arquivo são tratados em conformidade com a Lei Geral de Proteção de Dados (LGPD) e devem ser mantidos sob medidas adequadas de segurança.
    </div>
</div>
<!-- Footer placeholder: server-side drawing will add texts -->
<div class="footer">
    <div style="display:flex; justify-content:space-between; align-items:center; font-size:9pt; color:#555;">
        <div></div>
        <div></div>
    </div>
</div>

@php
    use App\Support\SocioeconomicoEtiquetas as SE;
    $s = $socio;
    $moradoresPdf = $moradores ?? $local->moradores;
@endphp
<div class="content" style="margin-top:12px;">
<div class="panel">
    <div class="grid-2">
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Código do imóvel') }}</th><td>{{ $local->loc_codigo_unico }}</td></tr>
                <tr><th>{{ __('Endereço') }}</th><td>{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}</td></tr>
                <tr><th>{{ __('Bairro / cidade') }}</th><td>{{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}</td></tr>
                <tr><th>{{ __('CEP') }}</th><td>{{ $local->loc_cep ?? '-' }}</td></tr>
            </table>
        </div>
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Tipo / zona') }}</th>
                    <td>
                        {{ SE::opcao('tipo_local_opcoes', $local->loc_tipo) ?? $local->loc_tipo ?? '-' }}
                        /
                        {{ SE::opcao('zona_opcoes', $local->loc_zona) ?? $local->loc_zona ?? '-' }}
                    </td>
                </tr>
                <tr><th>{{ __('Latitude / longitude') }}</th><td>{{ $local->loc_latitude ?? '-' }} / {{ $local->loc_longitude ?? '-' }}</td></tr>
                <tr><th>{{ __('Código localidade') }}</th><td>{{ $local->loc_codigo ?? '-' }}</td></tr>
                <tr><th>{{ __('Categoria / quarteirão') }}</th><td>{{ $local->loc_categoria ?? '-' }} / {{ $local->loc_quarteirao ?? '-' }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="panel section">
    <h2>{{ $titulos['entrevista'] ?? '1. Entrevista e domicílio' }}</h2>
    <div class="grid-2">
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Data da entrevista') }}</th><td>{{ $s?->lse_data_entrevista?->format('d/m/Y') ?? '-' }}</td></tr>
                <tr><th>{{ __('Condição da moradia') }}</th><td>{{ SE::opcao('condicao_casa_opcoes', $s?->lse_condicao_casa) }}</td></tr>
                <tr><th>{{ __('Posição de quem respondeu') }}</th><td>{{ SE::opcao('posicao_entrevistado_opcoes', $s?->lse_posicao_entrevistado) }}</td></tr>
            </table>
        </div>
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Telefone de contato') }}</th><td>{{ $s?->lse_telefone_contato ?? '-' }}</td></tr>
                <tr><th>{{ __('Nº moradores declarado') }}</th><td>{{ $s?->lse_n_moradores_declarado ?? '-' }}</td></tr>
                <tr><th>{{ __('Renda formal/informal') }}</th><td>{{ SE::opcao('renda_formal_informal_opcoes', $s?->lse_renda_formal_informal) }}</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="panel section">
    <h2>{{ $titulos['economia'] ?? '2. Economia do grupo familiar' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Renda familiar (faixa)') }}</th><td>{{ SE::municipioRenda($s?->lse_renda_familiar_faixa) }}</td></tr>
        <tr><th>{{ __('Principal fonte de renda') }}</th><td>{{ $s?->lse_principal_fonte_renda ?? '-' }}</td></tr>
        <tr><th>{{ __('Pessoas que contribuem') }}</th><td>{{ $s?->lse_qtd_contribuintes ?? '-' }}</td></tr>
        <tr><th>{{ __('Gastos mensais (faixa)') }}</th><td>{{ SE::opcao('gastos_mensais_faixa_opcoes', $s?->lse_gastos_mensais_faixa) }}</td></tr>
        <tr><th>{{ __('Benefícios sociais') }}</th><td>{{ $s?->lse_beneficios_sociais ?? '-' }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['proprietario'] ?? '3. Proprietário' }}</h2>
    <div class="grid-2">
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Situação da posse') }}</th><td>{{ SE::opcao('situacao_posse_opcoes', $s?->lse_situacao_posse) }}</td></tr>
                <tr><th>{{ __('Posse da área') }}</th><td>{{ SE::opcao('posse_area_opcoes', $s?->lse_posse_area) }}</td></tr>
                <tr><th>{{ __('Escritura') }}</th><td>{{ SE::opcao('escritura_opcoes', $s?->lse_escritura) }}</td></tr>
            </table>
        </div>
        <div class="cell">
            <table class="compact">
                <tr><th>{{ __('Nome') }}</th><td>{{ $s?->lse_proprietario_nome ?? '-' }}</td></tr>
                <tr><th>{{ __('Telefone') }}</th><td>{{ $s?->lse_proprietario_telefone ?? '-' }}</td></tr>
                <tr><th>{{ __('Endereço') }}</th><td>{{ $s?->lse_proprietario_endereco ?? '-' }}</td></tr>
                <tr><th>{{ __('Proprietário anterior') }}</th><td>{{ $s?->lse_proprietario_anterior_nome ?? '-' }} @if($s?->lse_proprietario_anterior_doc) ({{ $s->lse_proprietario_anterior_doc }}) @endif</td></tr>
            </table>
        </div>
    </div>
</div>

<div class="panel section">
    <h2>{{ $titulos['moradores'] ?? 'Composição familiar' }}</h2>
    @if(isset($moradorSelecionado) && $moradorSelecionado)
        <p class="small">{{ __('Ocupante selecionado') }}: <strong>{{ $moradorSelecionado->mor_nome ?? __('N/D') }}</strong></p>
    @endif
    <table class="compact">
        <thead>
            <tr>
                <th style="width:35%">{{ __('Nome / Profissão') }}</th>
                <th style="width:5%">{{ __('Ref.') }}</th>
                <th style="width:15%">{{ __('Parentesco') }}</th>
                <th style="width:8%">{{ __('Sexo') }}</th>
                <th style="width:12%">{{ __('Nasc.') }}</th>
                <th style="width:25%">{{ __('Escolaridade • Renda • Trabalho') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($moradoresPdf as $m)
                <tr>
                    <td>
                        <strong>{{ $m->mor_nome ?? '-' }}</strong>
                        @if($m->mor_profissao)
                            <div class="small">{{ $m->mor_profissao }}</div>
                        @endif
                        @if($m->mor_cpf || $m->mor_rg_numero)
                            <div class="small" style="margin-top:4px;">
                                @if($m->mor_cpf) <div>CPF: {{ $m->mor_cpf }}</div> @endif
                                @if($m->mor_rg_numero) <div>RG: {{ $m->mor_rg_numero }} @if($m->mor_rg_orgao) ({{ $m->mor_rg_orgao }}) @endif @if($m->mor_rg_expedicao) — {{ $m->mor_rg_expedicao?->format('d/m/Y') }}@endif</div> @endif
                            </div>
                        @endif
                    </td>
                    <td class="center">{{ $m->mor_referencia_familiar ? '★' : '-' }}</td>
                    <td>{{ SE::opcao('parentesco_opcoes', $m->mor_parentesco) }}</td>
                    <td>{{ SE::opcao('sexo_opcoes', $m->mor_sexo) }}</td>
                    <td>{{ $m->mor_data_nascimento?->format('d/m/Y') ?? '-' }}</td>
                    <td>
                        {{ SE::municipioEscolaridade($m->mor_escolaridade) }}
                        @if($m->mor_renda_faixa) • <span class="renda">{{ SE::municipioRenda($m->mor_renda_faixa) }}</span>@endif
                        @if($m->mor_situacao_trabalho) • {{ SE::municipioTrabalho($m->mor_situacao_trabalho) }}@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">{{ __('Nenhum morador cadastrado.') }}</td></tr>
            @endforelse
        </tbody>
    </table>
    {{-- Identificação: incluída no registro individual do ocupante acima. --}}
</div>

<div class="panel section">
    <h2>{{ $titulos['imovel_caracteristicas'] ?? '4. Características do imóvel' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Uso do imóvel') }}</th><td>{{ SE::opcao('uso_imovel_socio_opcoes', $s?->lse_uso_imovel) }}</td></tr>
        <tr><th>{{ __('Situação da posse') }}</th><td>{{ SE::opcao('situacao_posse_opcoes', $s?->lse_situacao_posse) }}</td></tr>
        <tr><th>{{ __('Material predominante') }}</th><td>{{ SE::opcao('material_predominante_opcoes', $s?->lse_material_predominante) }}</td></tr>
        <tr><th>{{ __('Condição da edificação') }}</th><td>{{ SE::opcao('condicao_edificacao_opcoes', $s?->lse_condicao_edificacao) }}</td></tr>
        <tr><th>{{ __('Cômodos / quartos / banheiros') }}</th><td>{{ $s?->lse_num_comodos ?? '-' }} / {{ $s?->lse_num_quartos ?? '-' }} / {{ $s?->lse_num_banheiros ?? '-' }}</td></tr>
        <tr><th>{{ __('Banheiros dentro / fora') }}</th><td>{{ $s?->lse_banheiro_dentro ?? '-' }} / {{ $s?->lse_banheiro_fora ?? '-' }}</td></tr>
        <tr><th>{{ __('Área externa') }}</th><td>{{ SE::opcao('area_externa_opcoes', $s?->lse_area_externa) }}</td></tr>
        <tr><th>{{ __('Área livre / quintal') }}</th><td>{{ $s?->lse_area_livre ?? '-' }}</td></tr>
        <tr><th>{{ __('Observações') }}</th><td>{{ $s?->lse_observacoes_imovel ?? '-' }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['cadastro_fisico'] ?? '5. Cadastro físico' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Confrontante frente') }}</th><td>{{ $s?->lse_viz_frente ?? '-' }}</td></tr>
        <tr><th>{{ __('Confrontante fundos') }}</th><td>{{ $s?->lse_viz_fundos ?? '-' }}</td></tr>
        <tr><th>{{ __('Confrontante direita') }}</th><td>{{ $s?->lse_viz_direita ?? '-' }}</td></tr>
        <tr><th>{{ __('Confrontante esquerda') }}</th><td>{{ $s?->lse_viz_esquerda ?? '-' }}</td></tr>
        <tr><th>{{ __('Tipologia') }}</th><td>{{ SE::opcao('tipologia_opcoes', $s?->lse_tipologia) }}</td></tr>
        <tr><th>{{ __('Tipo / implantação') }}</th><td>{{ SE::opcao('tipo_implantacao_opcoes', $s?->lse_tipo_implantacao) }}</td></tr>
        <tr><th>{{ __('Posição lote') }}</th><td>{{ SE::opcao('posicao_lote_opcoes', $s?->lse_posicao_lote) }}</td></tr>
        <tr><th>{{ __('Pavimentos') }}</th><td>{{ $s?->lse_num_pavimentos ?? '-' }}</td></tr>
        <tr><th>{{ __('Banheiro compartilhado') }}</th><td>{{ SE::simNaoBool($s?->lse_banheiro_compartilha) }}</td></tr>
        <tr><th>{{ __('Acesso ao imóvel') }}</th><td>{{ SE::opcao('acesso_imovel_opcoes', $s?->lse_acesso_imovel) }}</td></tr>
        <tr><th>{{ __('Entrada para') }}</th><td>{{ SE::opcao('entrada_para_opcoes', $s?->lse_entrada_para) }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['infraestrutura'] ?? '6. Infraestrutura' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Água') }}</th><td>{{ SE::opcao('infra_sim_nao_redes_opcoes', $s?->lse_abastecimento_agua) }}</td></tr>
        <tr><th>{{ __('Energia') }}</th><td>{{ SE::opcao('infra_energia_opcoes', $s?->lse_energia_eletrica) }}</td></tr>
        <tr><th>{{ __('Esgoto') }}</th><td>{{ SE::opcao('infra_esgoto_opcoes', $s?->lse_esgoto) }}</td></tr>
        <tr><th>{{ __('Lixo') }}</th><td>{{ SE::opcao('infra_lixo_opcoes', $s?->lse_coleta_lixo) }}</td></tr>
        <tr><th>{{ __('Pavimentação') }}</th><td>{{ SE::opcao('infra_pavimentacao_opcoes', $s?->lse_pavimentacao) }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['terreno'] ?? '7. Terreno' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Situação terreno') }}</th><td>{{ SE::opcao('situacao_terreno_opcoes', $s?->lse_situacao_terreno) }}</td></tr>
        <tr><th>{{ __('Tempo de residência') }}</th><td>{{ $s?->lse_tempo_residencia_texto ?? '-' }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['historico'] ?? '8. Histórico da posse' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Data ocupação') }}</th><td>{{ $s?->lse_data_ocupacao?->format('d/m/Y') ?? '-' }}</td></tr>
        <tr><th>{{ __('Compra e venda') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_houve_compra_venda) }}</td></tr>
        <tr><th>{{ __('Forma de aquisição') }}</th><td>{{ $s?->lse_forma_aquisicao ?? '-' }}</td></tr>
        <tr><th>{{ __('Como ocupou') }}</th><td>{{ $s?->lse_como_ocupou ?? '-' }}</td></tr>
        <tr><th>{{ __('Escritura') }}</th><td>{{ SE::opcao('escritura_opcoes', $s?->lse_escritura) }}</td></tr>
        <tr><th>{{ __('Promessa compra') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_contrato_promessa) }}</td></tr>
        <tr><th>{{ __('Documento quitado') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_documento_quitado) }}</td></tr>
        <tr><th>{{ __('Sabe local vendedor') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_sabe_local_vendedor) }}</td></tr>
        <tr><th>{{ __('IPTU') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_paga_iptu) }}@if($s?->lse_iptu_desde), {{ $s->lse_iptu_desde }}@endif</td></tr>
        <tr><th>{{ __('Situação legal') }}</th><td>{{ $s?->lse_situacao_legal_obs ?? '-' }}</td></tr>
    </table>
</div>

<div class="panel section">
    <h2>{{ $titulos['finalizacao'] ?? '9. Finalização' }}</h2>
    <table class="compact">
        <tr><th>{{ __('Local e data (texto)') }}</th><td>{{ $s?->lse_local_data_assinatura ?? '-' }}</td></tr>
    </table>
</div>

<p class="muted center">{{ __('Documento gerado pelo sistema em ') }}{{ now()->format('d/m/Y H:i') }}. {{ __('Os dados pessoais contidos neste documento são tratados conforme a LGPD e devem ser mantidos em segurança.') }}</p>
</div>
</body>
</html>
