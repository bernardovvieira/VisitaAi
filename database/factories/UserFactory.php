<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Senha padrão nos testes (hash de 'password').
     */
    protected static ?string $password;

    /**
     * Define o estado padrão do modelo.
     * use_perfil deve ser um dos valores do ENUM: gestor, agente_endemias, agente_saude.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'use_nome' => $this->faker->name(),
            'use_cpf' => $this->faker->unique()->numerify('###########'),
            'use_email' => $this->faker->unique()->safeEmail(),
            'use_senha' => static::$password ??= Hash::make('password'),
            'email_verified_at' => now(),
            'use_perfil' => $this->faker->randomElement(['gestor', 'agente_endemias', 'agente_saude']),
            'use_aprovado' => true,
            'use_data_criacao' => now(),
            'use_data_anonimizacao' => null,
            'remember_token' => Str::random(10),
            'fk_gestor_id' => null,
        ];
    }

    /**
     * Estado "não verificado" (e-mail ainda não confirmado).
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
