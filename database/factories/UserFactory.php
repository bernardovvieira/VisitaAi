<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'use_cpf' => $this->faker->unique()->cpf(false),
            'use_email' => $this->faker->unique()->safeEmail,
            'use_senha' => static::$password ??= Hash::make('senha123'),
            'use_perfil' => $this->faker->randomElement(['agente', 'gestor']),
            'use_aprovado' => true,
            'use_data_criacao' => now(),
            'use_data_anonimizacao' => null,
            'remember_token' => Str::random(10),
            'fk_gestor_id' => null, 
        ];
    }
}
