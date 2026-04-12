<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GestorUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function gestor(): User
    {
        Local::factory()->create();

        return User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function gestor_pode_acessar_lista_e_formulario_de_usuarios(): void
    {
        $gestor = $this->gestor();

        $this->actingAs($gestor)
            ->get(route('gestor.users.index'))
            ->assertOk();

        $this->actingAs($gestor)
            ->get(route('gestor.users.create'))
            ->assertOk();
    }

    #[Test]
    public function gestor_pode_cadastrar_usuario(): void
    {
        $gestor = $this->gestor();

        $payload = [
            'use_nome' => 'Agente Novo',
            'use_cpf' => '12345678901',
            'use_email' => 'agente.novo@example.com',
            'use_senha' => 'Senha@123',
            'use_senha_confirmation' => 'Senha@123',
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ];

        $this->actingAs($gestor)
            ->post(route('gestor.users.store'), $payload)
            ->assertRedirect(route('gestor.users.index'));

        $this->assertDatabaseHas('users', [
            'use_email' => 'agente.novo@example.com',
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function agente_nao_pode_acessar_rotas_de_gestao_de_usuarios(): void
    {
        Local::factory()->create();
        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $this->actingAs($agente)
            ->get(route('gestor.users.index'))
            ->assertForbidden();
    }

    #[Test]
    public function gestor_nao_pode_anonimizar_a_si_mesmo(): void
    {
        $gestor = $this->gestor();

        $this->actingAs($gestor)
            ->delete(route('gestor.users.destroy', $gestor))
            ->assertRedirect(route('gestor.users.index'));

        $this->assertDatabaseHas('users', [
            'use_id' => $gestor->use_id,
            'use_email' => $gestor->use_email,
            'use_data_anonimizacao' => null,
        ]);
    }

    #[Test]
    public function gestor_pode_anonimizar_outro_usuario(): void
    {
        $gestor = $this->gestor();
        $target = User::factory()->create([
            'use_perfil' => 'agente_saude',
            'use_aprovado' => 1,
            'fk_gestor_id' => $gestor->use_id,
        ]);

        $this->actingAs($gestor)
            ->delete(route('gestor.users.destroy', $target))
            ->assertRedirect(route('gestor.users.index'));

        $this->assertDatabaseHas('users', [
            'use_id' => $target->use_id,
            'use_nome' => 'Anonimizado (ref. '.$target->use_id.')',
            'use_aprovado' => 0,
        ]);
    }
}
