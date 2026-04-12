<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LocalCreatePageTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function gestor_pode_abrir_pagina_de_novo_local_sem_local_primario(): void
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);

        $response = $this->actingAs($gestor)
            ->get(route('gestor.locais.create'));

        $response->assertOk()
            ->assertSeeText('RG: expedição');
    }

    #[Test]
    public function agente_endemias_pode_abrir_pagina_de_novo_local_quando_ja_existe_primario(): void
    {
        Local::factory()->create();

        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $response = $this->actingAs($agente)
            ->get(route('agente.locais.create'));

        $response->assertOk()
            ->assertSeeText('RG: expedição');
    }
}
