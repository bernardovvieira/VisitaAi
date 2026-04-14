<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\LocalSocioeconomico;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Barryvdh\DomPDF\Facade\Pdf;

class RelatorioGestorTest extends TestCase
{
    use RefreshDatabase;

    protected function gestorAprovado(): User
    {
        return User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function gestor_acessa_pagina_relatorios_sem_erro(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        $agente = User::factory()->create([
            'fk_gestor_id' => $gestor->use_id,
        ]);
        Visita::create([
            'fk_usuario_id' => $agente->getKey(),
            'fk_local_id' => $local->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '1',
            'vis_pendencias' => false,
        ]);

        $this->actingAs($gestor)
            ->get(route('gestor.relatorios.index'))
            ->assertOk()
            ->assertSee(__('Cadastro complementar do imóvel'))
            ->assertSee(__('Imóveis no período: cadastro complementar'));
    }

    #[Test]
    public function tabela_relatorios_exibe_valores_traduzidos(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        
        // Criar ficha socioeconômica com valores enum
        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
            'lse_renda_familiar_faixa' => 'ate_1_sm',
            'lse_condicao_casa' => 'propria',
            'lse_situacao_posse' => 'propria_quitada',
            'lse_posicao_entrevistado' => 'titular',
            'lse_renda_formal_informal' => 'formal',
        ]);
        
        $agente = User::factory()->create([
            'fk_gestor_id' => $gestor->use_id,
        ]);
        Visita::create([
            'fk_usuario_id' => $agente->getKey(),
            'fk_local_id' => $local->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '1',
            'vis_pendencias' => false,
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.relatorios.index'));

        $response->assertOk();
        
        // Verifica que os valores TRADUZIDOS aparecem, não os valores crus
        $response->assertSee(__('Até 1 salário mínimo'));  // ate_1_sm traduzido
        $response->assertDontSee('ate_1_sm');               // valor cru NÃO deve aparecer
    }

    #[Test]
    public function relatorio_pdf_exporta_com_sucesso(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        
        LocalSocioeconomico::create([
            'fk_local_id' => $local->loc_id,
            'lse_data_entrevista' => now()->toDateString(),
            'lse_renda_familiar_faixa' => 'ate_2_sm',
        ]);

        $agente = User::factory()->create([
            'fk_gestor_id' => $gestor->use_id,
        ]);
        Visita::create([
            'fk_usuario_id' => $agente->getKey(),
            'fk_local_id' => $local->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '1',
            'vis_pendencias' => false,
        ]);

        // Teste a export em PDF
        $response = $this->actingAs($gestor)
            ->post(route('gestor.relatorios.pdf'), [
                'data_inicio' => now()->subDays(30)->toDateString(),
                'data_fim' => now()->toDateString(),
                'bairros' => [],
                'tipo_relatorio' => 'completo',
            ]);

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    #[Test]
    public function relatorio_pdf_memory_error_is_handled(): void
    {
        $gestor = $this->gestorAprovado();
        $local = Local::factory()->create();
        $agente = User::factory()->create([
            'fk_gestor_id' => $gestor->use_id,
        ]);
        Visita::create([
            'fk_usuario_id' => $agente->getKey(),
            'fk_local_id' => $local->getKey(),
            'vis_data' => now()->toDateString(),
            'vis_atividade' => '1',
            'vis_pendencias' => false,
        ]);

        // Simula erro de memória lançado pela biblioteca de PDF
        Pdf::shouldReceive('loadView')->once()->andThrow(new \ErrorException('Allowed memory size of 134217728 bytes exhausted'));

        $response = $this->actingAs($gestor)
            ->post(route('gestor.relatorios.pdf'), [
                'data_inicio' => now()->subDays(30)->toDateString(),
                'data_fim' => now()->toDateString(),
                'bairros' => [],
                'tipo_relatorio' => 'completo',
            ]);

        $response->assertRedirect(route('gestor.relatorios.index'));
        $response->assertSessionHas('error', __('Não foi possível gerar o PDF devido à falta de memória. Tente reduzir o período ou aplicar filtros mais restritos (por exemplo relatório individual).'));
    }
}
