<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'nome' => 'Test User',
            'cpf' => '12345678901',
            'email' => 'test@example.org',
            'password' => 'Senha123!',
            'password_confirmation' => 'Senha123!',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
    }

    public function test_registration_does_not_reveal_which_field_is_duplicate(): void
    {
        $existing = User::factory()->create();
        $otherCpfUser = User::factory()->make();

        $response = $this->post('/register', [
            'nome' => 'Outro usuário',
            'cpf' => preg_replace('/\D/', '', (string) $otherCpfUser->use_cpf),
            'email' => $existing->use_email,
            'password' => 'Senha123!',
            'password_confirmation' => 'Senha123!',
        ]);

        $response->assertSessionHasErrors('register');
        $msg = session('errors')->get('register')[0] ?? '';
        $this->assertStringNotContainsStringIgnoringCase('e-mail', $msg);
        $this->assertStringNotContainsStringIgnoringCase('CPF', $msg);
    }
}
