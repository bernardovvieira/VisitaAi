<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
