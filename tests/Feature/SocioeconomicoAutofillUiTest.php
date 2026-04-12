<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SocioeconomicoAutofillUiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function novo_local_exibe_ganchos_de_preenchimento_automatico_por_morador(): void
    {
        $gestor = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => 1,
        ]);

        $response = $this->actingAs($gestor)->get(route('gestor.locais.create'));

        $response->assertOk();
        $response->assertSee('syncSocioFromRows', false);
        $response->assertSee('data-autofill-from-ocupantes="1"', false);
        $response->assertDontSee('Resumo automático por moradores', false);
    }
}
