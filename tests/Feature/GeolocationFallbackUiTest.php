<?php

namespace Tests\Feature;

use App\Models\Local;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GeolocationFallbackUiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function create_page_exposes_multistep_geolocation_fallbacks(): void
    {
        Local::factory()->create();

        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $response = $this->actingAs($agente)->get(route('agente.locais.create'));

        $response->assertOk();
        $response->assertSee('tryIpGeolocationFallback', false);
        $response->assertSee('navigator.permissions.query', false);
        $response->assertSee('window.isSecureContext', false);
    }

    #[Test]
    public function edit_page_exposes_multistep_geolocation_fallbacks(): void
    {
        Local::factory()->create();
        $local = Local::factory()->create();

        $agente = User::factory()->create([
            'use_perfil' => 'agente_endemias',
            'use_aprovado' => 1,
        ]);

        $response = $this->actingAs($agente)->get(route('agente.locais.edit', $local));

        $response->assertOk();
        $response->assertSee('tryIpGeolocationFallback', false);
        $response->assertSee('navigator.permissions.query', false);
        $response->assertSee('window.isSecureContext', false);
    }
}
