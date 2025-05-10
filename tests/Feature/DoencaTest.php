<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Doenca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;

class DoencaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Cria um usuário com perfil gestor para os testes.
     */
    protected function gestor(): User
    {
        return User::factory()->create([
            'use_nome'     => 'Gestor Teste',
            'use_perfil'   => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function index_requer_autenticacao()
    {
        $response = $this->get(route('gestor.doencas.index'));
        $response->assertRedirect('/login');
    }

    #[Test]
    public function gestor_pode_listar_doencas()
    {
        $user = $this->gestor();
        Doenca::factory()->count(3)->create();

        $response = $this->actingAs($user)
                         ->get(route('gestor.doencas.index'));

        $response->assertStatus(200)
                 ->assertSeeText('Gerenciar Doenças');
    }

    #[Test]
    public function gestor_pode_criar_doenca_com_dados_validos()
    {
        $user = $this->gestor();
        $payload = [
            'doe_nome' => 'Teste Doença',
            'doe_sintomas' => ['Febre', 'Tosse'],
            'doe_transmissao' => ['Contato'],
            'doe_medidas_controle' => ['Isolamento'],
        ];

        $response = $this->actingAs($user)
                         ->post(route('gestor.doencas.store'), $payload);

        $response->assertRedirect(route('gestor.doencas.index'));
        $this->assertDatabaseHas('doencas', ['doe_nome' => 'Teste Doença']);
    }

    #[Test]
    public function store_valida_campos_obrigatorios()
    {
        $user = $this->gestor();

        $response = $this->actingAs($user)
                         ->post(route('gestor.doencas.store'), []);

        $response->assertSessionHasErrors([
            'doe_nome', 'doe_sintomas', 'doe_transmissao', 'doe_medidas_controle'
        ]);
    }

    #[Test]
    public function gestor_pode_atualizar_doenca()
    {
        $user = $this->gestor();
        $doenca = Doenca::factory()->create(['doe_nome' => 'Antiga']);

        $payload = [
            'doe_nome' => 'Atualizada',
            'doe_sintomas' => ['Sintoma1'],
            'doe_transmissao' => ['Modo1'],
            'doe_medidas_controle' => ['Medida1'],
        ];

        $response = $this->actingAs($user)
                         ->patch(route('gestor.doencas.update', $doenca), $payload);

        $response->assertRedirect(route('gestor.doencas.index'));
        $this->assertDatabaseHas('doencas', ['doe_nome' => 'Atualizada']);
    }

    #[Test]
    public function gestor_pode_excluir_doenca()
    {
        $user = $this->gestor();
        $doenca = Doenca::factory()->create();

        $response = $this->actingAs($user)
                         ->delete(route('gestor.doencas.destroy', $doenca));

        $response->assertRedirect(route('gestor.doencas.index'));
        $this->assertDatabaseMissing('doencas', ['doe_id' => $doenca->doe_id]);
    }
}
