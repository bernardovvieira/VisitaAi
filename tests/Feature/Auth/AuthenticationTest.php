<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create(['use_perfil' => 'gestor']);

        $response = $this->post('/login', [
            'use_email' => $user->use_email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('gestor.dashboard', [], false));
        $this->assertNotNull($user->fresh()->use_ultimo_login_em);
    }

    public function test_users_are_inactivated_and_cannot_login_after_two_months_without_access(): void
    {
        $user = User::factory()->create([
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
            'use_data_criacao' => now()->subMonthsNoOverflow(3),
            'use_ultimo_login_em' => now()->subMonthsNoOverflow(3),
        ]);

        $response = $this->from('/login')->post('/login', [
            'use_email' => $user->use_email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('use_email');
        $this->assertGuest();
        $this->assertFalse((bool) $user->fresh()->use_aprovado);
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'use_email' => $user->use_email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
