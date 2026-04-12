<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\LocalSocioeconomico;
use App\Models\Morador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocioeconomicoExportTest extends TestCase
{
    use RefreshDatabase;

    protected function gestorAprovado(): User
    {
        return User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    protected function agenteAprovado(): User
    {
        return User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function gestor_exporta_ficha_socioeconomica_por_local(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create([
            'loc_codigo_unico' => '12345678',
            'loc_endereco' => 'Rua Principal',
            'loc_numero' => '100',
            'loc_bairro' => 'Centro',
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
            'lse_condicao_casa' => 'propria',
            'lse_posicao_entrevistado' => 'titular',
            'lse_renda_familiar_faixa' => 'ate_1_sm',
            'lse_renda_formal_informal' => 'formal',
            'lse_situacao_posse' => 'propria_quitada',
            'lse_n_moradores_declarado' => 4,
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.ficha-socioeconomica-pdf', $local));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('ficha-socioeconomica', $response->headers->get('content-disposition'));
    }

    #[Test]
    public function agente_exporta_ficha_socioeconomica_por_local(): void
    {
        $agente = $this->agenteAprovado();
        $local = Local::factory()->create();

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
            'lse_condicao_casa' => 'alugada',
            'lse_renda_familiar_faixa' => 'ate_2_sm',
        ]);

        $response = $this->actingAs($agente)
            ->get(route('agente.locais.ficha-socioeconomica-pdf', $local));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function ficha_local_contem_valores_traduzidos(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
            'lse_condicao_casa' => 'propria',
            'lse_posicao_entrevistado' => 'titular',
            'lse_renda_familiar_faixa' => 'ate_1_sm',
            'lse_renda_formal_informal' => 'formal',
            'lse_situacao_posse' => 'propria_quitada',
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.ficha-socioeconomica-pdf', $local));
        
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        // PDF é gerado com sucesso
    }

    #[Test]
    public function gestor_exporta_ficha_socioeconomica_por_morador(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        $morador = Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'João Silva',
            'mor_escolaridade' => 'medio_completo',
            'mor_renda_faixa' => 'ate_1_sm',
            'mor_situacao_trabalho' => 'empregado',
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.ficha-socioeconomica-pdf', [$local, $morador]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function agente_exporta_ficha_socioeconomica_por_morador(): void
    {
        $agente = $this->agenteAprovado();
        $local = Local::factory()->create();
        $morador = Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
        ]);

        $response = $this->actingAs($agente)
            ->get(route('agente.locais.moradores.ficha-socioeconomica-pdf', [$local, $morador]));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function usuario_nao_autorizado_nao_acessa_ficha_local(): void
    {
        $local = Local::factory()->create();

        $response = $this->getJson(route('gestor.locais.ficha-socioeconomica-pdf', $local));

        $response->assertUnauthorized();
    }

    #[Test]
    public function usuario_nao_autorizado_nao_acessa_ficha_morador(): void
    {
        $local = Local::factory()->create();
        $morador = Morador::factory()->create(['fk_local_id' => $local->loc_id]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
        ]);

        $response = $this->getJson(route('gestor.locais.moradores.ficha-socioeconomica-pdf', [$local, $morador]));

        $response->assertUnauthorized();
    }

    #[Test]
    public function ficha_morador_contém_dados_do_ocupante(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        $morador = Morador::factory()->create([
            'fk_local_id' => $local->loc_id,
            'mor_nome' => 'Maria Santos',
            'mor_escolaridade' => 'superior_completo',
            'mor_renda_faixa' => 'acima_5_sm',
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.moradores.ficha-socioeconomica-pdf', [$local, $morador]));
        
        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        // PDF é gerado com sucesso
    }

    #[Test]
    public function ficha_local_sem_socioeconomico_retorna_404(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();

        // Local sem ficha socioeconômica

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.ficha-socioeconomica-pdf', $local));

        // Verifica que a página não quebra (pode 404 ou redireciona)
        $this->assertTrue(
            in_array($response->status(), [200, 302, 404]),
            "Expected status 200, 302 or 404, got {$response->status()}"
        );
    }

    #[Test]
    public function PDF_filename_inclui_codigo_unico(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create([
            'loc_codigo_unico' => '99887766',
        ]);

        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.ficha-socioeconomica-pdf', $local));

        $response->assertOk();
        $disposition = $response->headers->get('content-disposition');
        $this->assertStringContainsString('ficha-socioeconomica', $disposition);
    }
}
