<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A página de edição de perfil deve estar acessível para usuários autenticados.
     */
    #[Test]
    public function edit_page_is_displayed(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->get(route('profile.edit'))
            ->assertOk();
    }

    /**
     * Atualização de perfil com dados válidos deve funcionar e normalizar o e-mail em lowercase.
     */
    #[Test]
    public function profile_information_can_be_updated_with_valid_data(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $payload = [
            'name' => 'Test User',
            'email' => 'perfil-teste@example.com',
            'tema' => 'light',
        ];

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), $payload)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect(route('profile.edit'));

        // Verifica que o 'use_id' (PK), use_nome e use_email foram atualizados
        $this->assertDatabaseHas('users', [
            'use_id' => $user->use_id,
            'use_nome' => 'Test User',
            'use_email' => 'perfil-teste@example.com',
        ]);
    }

    /**
     * Campos 'name' e 'email' são obrigatórios.
     */
    #[Test]
    public function profile_update_requires_name_and_email(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), [])
            ->assertSessionHasErrors(['name', 'email', 'tema']);
    }

    /**
     * E-mail deve ter formato válido.
     */
    #[Test]
    public function profile_update_requires_valid_email(): void
    {
        /** @var \App\Models\User $user */
        $user = User::factory()->create();
        $payload = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'tema' => 'light',
        ];

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), $payload)
            ->assertSessionHasErrors('email');
    }

    /**
     * E-mail deve ser único entre usuários.
     */
    #[Test]
    public function profile_update_requires_unique_email(): void
    {
        $existing = User::factory()->create(['use_email' => '179835@upf.br']);
        /** @var \App\Models\User $user */
        $user = User::factory()->create();

        $payload = [
            'name' => 'Another User',
            'email' => '179835@upf.br',
            'tema' => 'light',
        ];

        $this
            ->actingAs($user)
            ->patch(route('profile.update'), $payload)
            ->assertSessionHasErrors('email');
    }
}
