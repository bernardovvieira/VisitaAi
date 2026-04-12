<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VisitasPendenciasCalloutTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function gestor_ve_apenas_cinco_pendencias_iniciais_com_botao_ver_mais(): void
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);

        for ($i = 1; $i <= 6; $i++) {
            $local = Local::factory()->create([
                'loc_codigo_unico' => (string) (80000000 + $i),
                'loc_endereco' => 'Rua '.chr(64 + $i),
                'loc_numero' => (string) (100 + $i),
                'loc_bairro' => 'Centro',
                'loc_cidade' => 'Soledade',
                'loc_estado' => 'RS',
            ]);

            Visita::create([
                'fk_usuario_id' => $gestor->use_id,
                'fk_local_id' => $local->loc_id,
                'vis_data' => "2025-04-0{$i}",
                'vis_ciclo' => '04/25',
                'vis_atividade' => '2',
                'vis_pendencias' => true,
                'vis_concluida' => false,
            ]);
        }

        $html = $this->actingAs($gestor)->get(route('gestor.visitas.index'))->assertOk()->getContent();

        $this->assertStringContainsString('Pendências sem revisita', $html);
        $this->assertStringContainsString('Ver mais', $html);
        $this->assertStringContainsString('x-show="expandedPendencias"', $html);
        $this->assertStringContainsString('x-cloak', $html);
        $this->assertStringContainsString('Rua A', $html);
        $this->assertStringContainsString('Rua E', $html);
    }
}