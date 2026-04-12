<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityAccessControlTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function usuario_nao_aprovado_e_redirecionado_para_pendente(): void
    {
        Local::factory()->create();
        $user = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 0,
        ]);

        $this->actingAs($user)
            ->get(route('agente.dashboard'))
            ->assertRedirect(route('pendente'));
    }

    #[Test]
    public function perfil_incorreto_recebe_403_em_area_restrita(): void
    {
        Local::factory()->create();
        $user = User::factory()->create([
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
        ]);

        $this->actingAs($user)
            ->get(route('agente.dashboard'))
            ->assertForbidden();
    }

    #[Test]
    public function sugestoes_de_doencas_sem_autenticacao_retorna_json_401(): void
    {
        $response = $this->getJson(route('agente.sugestoes-doencas'));

        $response->assertUnauthorized();
        $response->assertJson([
            'message' => 'Sessão expirada. Recarregue a página.',
        ]);
    }
}
