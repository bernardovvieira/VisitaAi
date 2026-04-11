<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>{{ __('Ficha socioeconômica') }}: {{ $local->loc_codigo_unico }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9pt; color: #111; }
        h1 { font-size: 13pt; margin: 0 0 6px 0; }
        h2 { font-size: 10pt; margin: 14px 0 6px 0; border-bottom: 1px solid #333; padding-bottom: 2px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th, td { border: 1px solid #444; padding: 4px 6px; vertical-align: top; }
        th { background: #f0f0f0; font-weight: bold; text-align: left; width: 32%; }
        .muted { color: #555; font-size: 8pt; }
        .small { font-size: 8pt; }
    </style>
</head>
<body>
@php
    use App\Support\SocioeconomicoEtiquetas as SE;
    $s = $socio;
    $moradoresPdf = $moradores ?? $local->moradores;
@endphp

<h1>{{ __('CADASTRO SOCIOECONÔMICO') }}</h1>
<p class="muted">{{ config('app.name') }} · {{ __('Código do imóvel') }}: <strong>{{ $local->loc_codigo_unico }}</strong></p>
<p class="small">{{ $local->loc_endereco }}, {{ $local->loc_numero ?? 'S/N' }}, {{ $local->loc_bairro }}, {{ $local->loc_cidade }}/{{ $local->loc_estado }}, CEP {{ $local->loc_cep }}</p>

<h2>{{ $titulos['entrevista'] ?? '1. Entrevista e domicílio' }}</h2>
<table>
    <tr><th>{{ __('Data da entrevista') }}</th><td>{{ $s?->lse_data_entrevista?->format('d/m/Y') ?? '-' }}</td></tr>
    <tr><th>{{ __('Telefone de contato') }}</th><td>{{ $s?->lse_telefone_contato ?? '-' }}</td></tr>
    <tr><th>{{ __('Condição da moradia') }}</th><td>{{ SE::opcao('condicao_casa_opcoes', $s?->lse_condicao_casa) }}</td></tr>
    <tr><th>{{ __('Posição de quem respondeu') }}</th><td>{{ SE::opcao('posicao_entrevistado_opcoes', $s?->lse_posicao_entrevistado) }}</td></tr>
    <tr><th>{{ __('Nº moradores declarado') }}</th><td>{{ $s?->lse_n_moradores_declarado ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['economia'] ?? '2. Economia do grupo familiar' }}</h2>
<table>
    <tr><th>{{ __('Renda formal/informal') }}</th><td>{{ SE::opcao('renda_formal_informal_opcoes', $s?->lse_renda_formal_informal) }}</td></tr>
    <tr><th>{{ __('Renda familiar (faixa)') }}</th><td>{{ SE::municipioRenda($s?->lse_renda_familiar_faixa) }}</td></tr>
    <tr><th>{{ __('Principal fonte de renda') }}</th><td>{{ $s?->lse_principal_fonte_renda ?? '-' }}</td></tr>
    <tr><th>{{ __('Pessoas que contribuem') }}</th><td>{{ $s?->lse_qtd_contribuintes ?? '-' }}</td></tr>
    <tr><th>{{ __('Gastos mensais (faixa)') }}</th><td>{{ SE::opcao('gastos_mensais_faixa_opcoes', $s?->lse_gastos_mensais_faixa) }}</td></tr>
    <tr><th>{{ __('Benefícios sociais') }}</th><td>{{ $s?->lse_beneficios_sociais ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['proprietario'] ?? '3. Proprietário' }}</h2>
<table>
    <tr><th>{{ __('Nome') }}</th><td>{{ $s?->lse_proprietario_nome ?? '-' }}</td></tr>
    <tr><th>{{ __('Telefone') }}</th><td>{{ $s?->lse_proprietario_telefone ?? '-' }}</td></tr>
    <tr><th>{{ __('Endereço') }}</th><td>{{ $s?->lse_proprietario_endereco ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['moradores'] ?? 'Composição familiar' }}</h2>
@if(isset($moradorSelecionado) && $moradorSelecionado)
    <p class="small">{{ __('Ocupante selecionado') }}: <strong>{{ $moradorSelecionado->mor_nome ?? __('N/D') }}</strong></p>
@endif
<table>
    <thead>
        <tr>
            <th>{{ __('Nome') }}</th>
            <th>{{ __('Ref.') }}</th>
            <th>{{ __('Parentesco') }}</th>
            <th>{{ __('Sexo') }}</th>
            <th>{{ __('Nasc.') }}</th>
            <th>{{ __('Est. civil') }}</th>
            <th>{{ __('Escolaridade') }}</th>
            <th>{{ __('Profissão') }}</th>
            <th>{{ __('Renda (faixa)') }}</th>
            <th>{{ __('Trabalho') }}</th>
        </tr>
    </thead>
    <tbody>
        @forelse($moradoresPdf as $m)
            <tr>
                <td>{{ $m->mor_nome ?? '-' }}</td>
                <td>{{ $m->mor_referencia_familiar ? '★' : '-' }}</td>
                <td>{{ SE::opcao('parentesco_opcoes', $m->mor_parentesco) }}</td>
                <td>{{ SE::opcao('sexo_opcoes', $m->mor_sexo) }}</td>
                <td>{{ $m->mor_data_nascimento?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ SE::opcao('estado_civil_opcoes', $m->mor_estado_civil) }}</td>
                <td>{{ SE::municipioEscolaridade($m->mor_escolaridade) }}</td>
                <td>{{ $m->mor_profissao ?? '-' }}</td>
                <td>{{ SE::municipioRenda($m->mor_renda_faixa) }}</td>
                <td>{{ SE::municipioTrabalho($m->mor_situacao_trabalho) }}</td>
            </tr>
        @empty
            <tr><td colspan="10">{{ __('Nenhum morador cadastrado.') }}</td></tr>
        @endforelse
    </tbody>
</table>
<p class="small">{{ __('Documentos (RG/CPF) foram omitidos do PDF por padrão; constam no sistema quando informados.') }}</p>

<h2>{{ $titulos['imovel_caracteristicas'] ?? '4. Características do imóvel' }}</h2>
<table>
    <tr><th>{{ __('Uso') }}</th><td>{{ SE::opcao('uso_imovel_socio_opcoes', $s?->lse_uso_imovel) }}</td></tr>
    <tr><th>{{ __('Posse') }}</th><td>{{ SE::opcao('situacao_posse_opcoes', $s?->lse_situacao_posse) }}</td></tr>
    <tr><th>{{ __('Material') }}</th><td>{{ SE::opcao('material_predominante_opcoes', $s?->lse_material_predominante) }}</td></tr>
    <tr><th>{{ __('Condição') }}</th><td>{{ SE::opcao('condicao_edificacao_opcoes', $s?->lse_condicao_edificacao) }}</td></tr>
    <tr><th>{{ __('Cômodos / quartos / banheiros') }}</th><td>{{ $s?->lse_num_comodos ?? '-' }} / {{ $s?->lse_num_quartos ?? '-' }} / {{ $s?->lse_num_banheiros ?? '-' }}</td></tr>
    <tr><th>{{ __('Área externa') }}</th><td>{{ SE::opcao('area_externa_opcoes', $s?->lse_area_externa) }}</td></tr>
    <tr><th>{{ __('Área livre / quintal') }}</th><td>{{ $s?->lse_area_livre ?? '-' }}</td></tr>
    <tr><th>{{ __('Observações') }}</th><td>{{ $s?->lse_observacoes_imovel ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['cadastro_fisico'] ?? '5. Cadastro físico' }}</h2>
<table>
    <tr><th>{{ __('Vizinho frente') }}</th><td>{{ $s?->lse_viz_frente ?? '-' }}</td></tr>
    <tr><th>{{ __('Vizinho fundos') }}</th><td>{{ $s?->lse_viz_fundos ?? '-' }}</td></tr>
    <tr><th>{{ __('Vizinho direita') }}</th><td>{{ $s?->lse_viz_direita ?? '-' }}</td></tr>
    <tr><th>{{ __('Vizinho esquerda') }}</th><td>{{ $s?->lse_viz_esquerda ?? '-' }}</td></tr>
    <tr><th>{{ __('Tipologia') }}</th><td>{{ SE::opcao('tipologia_opcoes', $s?->lse_tipologia) }}</td></tr>
    <tr><th>{{ __('Implantação') }}</th><td>{{ SE::opcao('tipo_implantacao_opcoes', $s?->lse_tipo_implantacao) }}</td></tr>
    <tr><th>{{ __('Posição lote') }}</th><td>{{ SE::opcao('posicao_lote_opcoes', $s?->lse_posicao_lote) }}</td></tr>
    <tr><th>{{ __('Pavimentos') }}</th><td>{{ $s?->lse_num_pavimentos ?? '-' }}</td></tr>
    <tr><th>{{ __('Banheiros dentro/fora') }}</th><td>{{ $s?->lse_banheiro_dentro ?? '-' }} / {{ $s?->lse_banheiro_fora ?? '-' }}</td></tr>
    <tr><th>{{ __('Banheiro compartilhado') }}</th><td>{{ SE::simNaoBool($s?->lse_banheiro_compartilha) }}</td></tr>
    <tr><th>{{ __('Acesso') }}</th><td>{{ SE::opcao('acesso_imovel_opcoes', $s?->lse_acesso_imovel) }}</td></tr>
    <tr><th>{{ __('Entrada para') }}</th><td>{{ SE::opcao('entrada_para_opcoes', $s?->lse_entrada_para) }}</td></tr>
</table>

<h2>{{ $titulos['infraestrutura'] ?? '6. Infraestrutura' }}</h2>
<table>
    <tr><th>{{ __('Água') }}</th><td>{{ SE::opcao('infra_sim_nao_redes_opcoes', $s?->lse_abastecimento_agua) }}</td></tr>
    <tr><th>{{ __('Energia') }}</th><td>{{ SE::opcao('infra_energia_opcoes', $s?->lse_energia_eletrica) }}</td></tr>
    <tr><th>{{ __('Esgoto') }}</th><td>{{ SE::opcao('infra_esgoto_opcoes', $s?->lse_esgoto) }}</td></tr>
    <tr><th>{{ __('Lixo') }}</th><td>{{ SE::opcao('infra_lixo_opcoes', $s?->lse_coleta_lixo) }}</td></tr>
    <tr><th>{{ __('Pavimentação') }}</th><td>{{ SE::opcao('infra_pavimentacao_opcoes', $s?->lse_pavimentacao) }}</td></tr>
</table>

<h2>{{ $titulos['terreno'] ?? '7. Terreno' }}</h2>
<table>
    <tr><th>{{ __('Situação terreno') }}</th><td>{{ SE::opcao('situacao_terreno_opcoes', $s?->lse_situacao_terreno) }}</td></tr>
    <tr><th>{{ __('Posse da área') }}</th><td>{{ SE::opcao('posse_area_opcoes', $s?->lse_posse_area) }}</td></tr>
    <tr><th>{{ __('Tempo de residência') }}</th><td>{{ $s?->lse_tempo_residencia_texto ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['historico'] ?? '8. Histórico da posse' }}</h2>
<table>
    <tr><th>{{ __('Data ocupação') }}</th><td>{{ $s?->lse_data_ocupacao?->format('d/m/Y') ?? '-' }}</td></tr>
    <tr><th>{{ __('Compra e venda') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_houve_compra_venda) }}</td></tr>
    <tr><th>{{ __('Forma de aquisição') }}</th><td>{{ $s?->lse_forma_aquisicao ?? '-' }}</td></tr>
    <tr><th>{{ __('Como ocupou') }}</th><td>{{ $s?->lse_como_ocupou ?? '-' }}</td></tr>
    <tr><th>{{ __('Escritura') }}</th><td>{{ SE::opcao('escritura_opcoes', $s?->lse_escritura) }}</td></tr>
    <tr><th>{{ __('Promessa compra') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_contrato_promessa) }}</td></tr>
    <tr><th>{{ __('Documento quitado') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_documento_quitado) }}</td></tr>
    <tr><th>{{ __('Sabe local vendedor') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_sabe_local_vendedor) }}</td></tr>
    <tr><th>{{ __('IPTU') }}</th><td>{{ SE::opcao('sim_nao_curto_opcoes', $s?->lse_paga_iptu) }}@if($s?->lse_iptu_desde), {{ $s->lse_iptu_desde }}@endif</td></tr>
    <tr><th>{{ __('Proprietário anterior') }}</th><td>{{ $s?->lse_proprietario_anterior_nome ?? '-' }} @if($s?->lse_proprietario_anterior_doc) ({{ $s->lse_proprietario_anterior_doc }}) @endif</td></tr>
    <tr><th>{{ __('Situação legal (obs.)') }}</th><td>{{ $s?->lse_situacao_legal_obs ?? '-' }}</td></tr>
</table>

<h2>{{ $titulos['finalizacao'] ?? '9. Finalização' }}</h2>
<table>
    <tr><th>{{ __('Local e data (texto)') }}</th><td>{{ $s?->lse_local_data_assinatura ?? '-' }}</td></tr>
</table>

<p class="muted" style="margin-top: 16px;">{{ __('Documento gerado pelo sistema em ') }}{{ now()->format('d/m/Y H:i') }}.</p>
</body>
</html>
