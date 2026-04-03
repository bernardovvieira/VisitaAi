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
    public function gestor_ve_aviso_sem_export_quando_sem_ocupantes(): void
    {
        $user = $this->gestorAprovado();

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes'));

        $response->assertOk()
            ->assertSee(config('visitaai_municipio.indicadores.export_csv_disabled_hint'), false);
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

    #[Test]
    public function gestor_export_csv_redireciona_sem_ocupantes(): void
    {
        $user = $this->gestorAprovado();

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes.export'));

        $response->assertRedirect(route('gestor.indicadores.ocupantes'));
        $response->assertSessionHas(
            'warning',
            config('visitaai_municipio.indicadores.export_csv_flash_sem_dados')
        );
    }

    #[Test]
    public function gestor_baixa_csv_indicadores(): void
    {
        $user = $this->gestorAprovado();
        $tituloCsv = (string) config('visitaai_municipio.indicadores.csv_titulo');
        $local = Local::factory()->create(['loc_bairro' => 'Bairro Teste']);
        Morador::factory()->count(5)->create(['fk_local_id' => $local->loc_id]);

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes.export'));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $cd = $response->headers->get('Content-Disposition');
        $this->assertStringContainsString('attachment', (string) $cd);
        $this->assertStringContainsString('.csv', (string) $cd);

        $body = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $body);
        $this->assertStringContainsString($tituloCsv, $body);
        $this->assertStringContainsString('Bairro Teste', $body);
        $this->assertStringContainsString('Resumo global', $body);
    }

    #[Test]
    public function ace_nao_exporta_csv_indicadores(): void
    {
        Local::factory()->create();
        $ace = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $this->actingAs($ace)
            ->get(route('gestor.indicadores.ocupantes.export'))
            ->assertForbidden();
    }
}
