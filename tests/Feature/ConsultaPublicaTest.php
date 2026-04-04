<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ConsultaPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_publica_responde_sem_erro(): void
    {
        $response = $this->get(route('consulta.index'));

        $response->assertOk();
        $response->assertSee(__('Código do imóvel'), false);
    }

    public function test_consulta_codigo_desconhecido_redireciona_sem_500(): void
    {
        $response = $this->get(route('consulta.codigo', ['codigo' => '99999999']));

        $response->assertRedirect();
        $response->assertSessionHas('erro');
    }

    public function test_index_publica_com_doenca_em_texto_simples_nao_gera_500(): void
    {
        DB::table('doencas')->insert([
            'doe_nome' => 'Dengue (teste)',
            'doe_sintomas' => 'Febre, dor no corpo',
            'doe_transmissao' => 'Picada do mosquito',
            'doe_medidas_controle' => 'Eliminar criadouros',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->get(route('consulta.index'));

        $response->assertOk();
        $response->assertSee('Febre', false);
    }
}
