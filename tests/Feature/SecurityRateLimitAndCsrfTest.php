<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityRateLimitAndCsrfTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function login_store_route_usa_middleware_csrf(): void
    {
        $route = $this->routeByName('login.store');

        $this->assertContains('web', $route->middleware());

        $bootstrap = file_get_contents(base_path('bootstrap/app.php'));
        $this->assertIsString($bootstrap);
        $this->assertStringContainsString('ValidateCsrfToken::class', $bootstrap);
    }

    #[Test]
    public function profile_destroy_route_usa_middleware_csrf(): void
    {
        $route = $this->routeByName('profile.destroy');

        $this->assertContains('web', $route->middleware());

        $bootstrap = file_get_contents(base_path('bootstrap/app.php'));
        $this->assertIsString($bootstrap);
        $this->assertStringContainsString('ValidateCsrfToken::class', $bootstrap);
    }

    #[Test]
    public function login_e_bloqueado_apos_tres_tentativas_no_mesmo_minuto(): void
    {
        for ($i = 0; $i < 3; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.11'])
                ->post(route('login.store'), [
                    'use_email' => 'inexistente@example.com',
                    'password' => 'senha-invalida',
                ])
                ->assertStatus(302);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.11'])
            ->post(route('login.store'), [
                'use_email' => 'inexistente@example.com',
                'password' => 'senha-invalida',
            ])
            ->assertStatus(429);
    }

    #[Test]
    public function reset_de_senha_e_bloqueado_apos_cinco_tentativas_no_mesmo_minuto(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.12'])
                ->post(route('password.email'), [
                    'email' => 'inexistente@example.com',
                ])
                ->assertStatus(302);
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.10.10.12'])
            ->post(route('password.email'), [
                'email' => 'inexistente@example.com',
            ])
            ->assertStatus(429);
    }

    #[Test]
    public function exclusao_de_perfil_e_bloqueada_apos_cinco_tentativas_no_mesmo_minuto(): void
    {
        $user = User::factory()->create();

        for ($i = 0; $i < 5; $i++) {
            $this->actingAs($user)
                ->withServerVariables(['REMOTE_ADDR' => '10.10.10.13'])
                ->delete(route('profile.destroy'), [
                    'password' => 'senha-invalida',
                ])
                ->assertStatus(302);
        }

        $this->actingAs($user)
            ->withServerVariables(['REMOTE_ADDR' => '10.10.10.13'])
            ->delete(route('profile.destroy'), [
                'password' => 'senha-invalida',
            ])
            ->assertStatus(429);
    }

    private function routeByName(string $name): IlluminateRoute
    {
        $route = Route::getRoutes()->getByName($name);
        $this->assertNotNull($route, 'Rota não encontrada: '.$name);

        return $route;
    }
}
