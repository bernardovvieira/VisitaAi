<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\Morador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class IndicadoresOcupantesTest extends TestCase
{
    use RefreshDatabase;

    protected function gestorAprovado(): User
    {
        Local::factory()->create();

        return User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);
    }

    #[Test]
    public function gestor_acessa_painel_indicadores_sem_export_csv(): void
    {
        $user = $this->gestorAprovado();

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes'));

        $response->assertOk()
            ->assertDontSee('Exportar CSV', false);
    }

    #[Test]
    public function gestor_acessa_painel_indicadores(): void
    {
        $user = $this->gestorAprovado();
        $local = Local::factory()->create(['loc_bairro' => 'Centro']);
        Morador::factory()->count(6)->create(['fk_local_id' => $local->loc_id]);

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes'));

        $response->assertStatus(200)
            ->assertSeeText(config('visitaai_municipio.indicadores.titulo_pagina'), false)
            ->assertSeeText('Centro', false);
    }

    #[Test]
    public function ace_nao_acessa_indicadores_gestor(): void
    {
        Local::factory()->create();
        $ace = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $this->actingAs($ace)
            ->get(route('gestor.indicadores.ocupantes'))
            ->assertForbidden();
    }
}
