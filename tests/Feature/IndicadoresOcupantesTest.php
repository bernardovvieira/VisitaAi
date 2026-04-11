<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\Morador;
use App\Models\User;
use App\Services\Municipio\IndicadoresOcupantesMunicipioService;
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
    public function gestor_ve_botao_export_csv_no_painel(): void
    {
        $user = $this->gestorAprovado();

        $response = $this->actingAs($user)->get(route('gestor.indicadores.ocupantes'));

        $response->assertOk()
            ->assertSeeText((string) config('visitaai_municipio.indicadores.botao_export_csv'), false);
    }

    #[Test]
    public function gestor_exporta_csv_com_cruzamento_completo(): void
    {
        $user = $this->gestorAprovado();
        $local = Local::factory()->create();
        Morador::factory()->count(2)->create([
            'fk_local_id' => $local->loc_id,
            'mor_escolaridade' => 'medio_completo',
            'mor_renda_faixa' => 'ate_1_sm',
        ]);

        $raw = $this->actingAs($user)
            ->get(route('gestor.indicadores.ocupantes.export'))
            ->assertOk()
            ->streamedContent();

        $this->assertStringContainsString('AVISO_LEGISLACAO_FEDERAL_EXPORTACAO', $raw);
        $this->assertStringContainsString('CRUZAMENTO_ESCOLARIDADE_RENDA_COMPLETO', $raw);
        $this->assertStringContainsString('SEXO', $raw);
        $this->assertStringContainsString('ocupantes_referencia_familiar', $raw);
        $this->assertStringContainsString('medio_completo', $raw);
        $this->assertStringContainsString(',2', $raw);
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
    public function painel_indicadores_inclui_completude_e_cruzamento(): void
    {
        $user = $this->gestorAprovado();
        $local = Local::factory()->create(['loc_bairro' => 'Centro']);
        Morador::factory()->count(6)->create([
            'fk_local_id' => $local->loc_id,
            'mor_escolaridade' => 'medio_completo',
            'mor_renda_faixa' => 'ate_1_sm',
        ]);

        $html = $this->actingAs($user)
            ->get(route('gestor.indicadores.ocupantes'))
            ->assertOk()
            ->getContent();

        $tituloCompletude = (string) config('visitaai_municipio.indicadores.titulo_secao_completude', '');
        $this->assertNotSame('', $tituloCompletude);
        $this->assertStringContainsString($tituloCompletude, $html);

        $tituloCruzamento = (string) config('visitaai_municipio.indicadores.titulo_secao_cruzamento', '');
        $this->assertStringContainsString($tituloCruzamento, $html);

        $app = $this->app->make(IndicadoresOcupantesMunicipioService::class);
        $painel = $app->painelCompleto();
        $this->assertArrayHasKey('sexo', $painel);
        $this->assertArrayHasKey('ocupantes_referencia_familiar', $painel['resumo']);
        $this->assertSame(100, $painel['completude']['pct_escolaridade_informada']);
        $this->assertSame(100, $painel['completude']['pct_renda_informada']);
        $cruz = $painel['cruzamento_escolaridade_renda']['celulas']['medio_completo']['ate_1_sm'];
        $this->assertSame(6, $cruz['count']);
        $this->assertFalse($cruz['suprimido']);
    }

    #[Test]
    public function cruzamento_suprime_celulas_com_poucos_registros(): void
    {
        $local = Local::factory()->create();
        Morador::factory()->count(4)->create([
            'fk_local_id' => $local->loc_id,
            'mor_escolaridade' => 'superior_completo',
            'mor_renda_faixa' => 'acima_3_sm',
        ]);

        $app = $this->app->make(IndicadoresOcupantesMunicipioService::class);
        $painel = $app->painelCompleto();
        $cruz = $painel['cruzamento_escolaridade_renda']['celulas']['superior_completo']['acima_3_sm'];
        $this->assertTrue($cruz['suprimido']);
        $this->assertNull($cruz['count']);
    }
}
