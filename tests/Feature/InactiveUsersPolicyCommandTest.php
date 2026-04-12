<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InactiveUsersPolicyCommandTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function comando_inativa_usuarios_sem_login_ha_mais_de_dois_meses(): void
    {
        $inativo = User::factory()->create([
            'use_aprovado' => true,
            'use_data_criacao' => now()->subMonthsNoOverflow(4),
            'use_ultimo_login_em' => now()->subMonthsNoOverflow(3),
        ]);

        $ativo = User::factory()->create([
            'use_aprovado' => true,
            'use_data_criacao' => now()->subMonthsNoOverflow(4),
            'use_ultimo_login_em' => now()->subDays(20),
        ]);

        $this->artisan('users:inactivate-inactive')
            ->expectsOutput('Usuários inativados por inatividade: 1')
            ->assertExitCode(0);

        $this->assertFalse((bool) $inativo->fresh()->use_aprovado);
        $this->assertTrue((bool) $ativo->fresh()->use_aprovado);
    }
}
