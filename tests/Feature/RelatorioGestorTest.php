<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
}
