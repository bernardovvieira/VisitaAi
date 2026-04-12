<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InfrastructureEndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config()->set('auth.guards.sanctum', [
            'driver' => 'session',
            'provider' => 'users',
        ]);
    }

    #[Test]
    public function healthcheck_up_responde_ok(): void
    {
        $this->get('/up')->assertOk();
    }

    #[Test]
    public function api_user_exige_autenticacao(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
    }

    #[Test]
    public function api_user_retorna_usuario_autenticado_por_sanctum(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/user');

        $response->assertOk();
        $response->assertJsonPath('use_id', $user->use_id);
        $response->assertJsonPath('use_email', $user->use_email);
    }
}
