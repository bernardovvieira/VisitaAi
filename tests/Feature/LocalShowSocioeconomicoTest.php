<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\LocalSocioeconomico;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocalShowSocioeconomicoTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function gestor_ve_dados_socioeconomicos_na_tela_do_local(): void
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);

        $local = Local::factory()->create([
            'loc_codigo_unico' => '87654321',
            'loc_endereco' => 'Rua Venancio Aires',
            'loc_numero' => '947',
            'loc_bairro' => 'Centro',
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => '2025-04-01',
            'lse_condicao_casa' => 'boa',
            'lse_posicao_entrevistado' => 'morador',
            'lse_telefone_contato' => '(54) 99999-0000',
            'lse_n_moradores_declarado' => 4,
            'lse_renda_formal_informal' => 'formal',
            'lse_principal_fonte_renda' => 'Aposentadoria',
            'lse_renda_familiar_faixa' => 'ate_2_sm',
            'lse_qtd_contribuintes' => 2,
            'lse_beneficios_sociais' => 'Bolsa Família',
            'lse_uso_imovel' => 'residencial',
            'lse_situacao_posse' => 'proprio',
            'lse_material_predominante' => 'alvenaria',
            'lse_condicao_edificacao' => 'boa',
            'lse_num_comodos' => 6,
            'lse_num_quartos' => 3,
            'lse_num_banheiros' => 2,
            'lse_abastecimento_agua' => 'rede_publica',
            'lse_energia_eletrica' => 'sim',
            'lse_esgoto' => 'rede_publica',
            'lse_coleta_lixo' => 'sim',
            'lse_pavimentacao' => 'sim',
            'lse_data_ocupacao' => '2018-01-10',
            'lse_paga_iptu' => 'sim',
            'lse_iptu_desde' => '2019',
            'lse_houve_compra_venda' => 'nao',
            'lse_escritura' => 'sim',
            'lse_contrato_promessa' => 'nao',
            'lse_documento_quitado' => 'sim',
            'lse_sabe_local_vendedor' => 'sim',
            'lse_proprietario_anterior_nome' => 'Joao da Silva',
            'lse_proprietario_anterior_doc' => '123.456.789-00',
            'lse_situacao_legal_obs' => 'Regular',
            'lse_local_data_assinatura' => 'Soledade, 01/04/2025',
        ]);

        $response = $this->actingAs($gestor)->get(route('gestor.locais.show', $local));

        $response->assertOk()
            ->assertSeeText('Cadastro socioeconômico', false)
            ->assertSeeText('Aposentadoria', false)
            ->assertSeeText('Bolsa Família', false)
            ->assertSeeText('Rua Venancio Aires', false)
            ->assertSeeText('2025', false);
    }
}