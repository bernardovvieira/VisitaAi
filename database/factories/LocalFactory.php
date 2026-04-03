<?php

namespace Database\Factories;

use App\Models\Local;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Local>
 */
class LocalFactory extends Factory
{
    protected $model = Local::class;

    public function definition(): array
    {
        return [
            'loc_cep' => '99000000',
            'loc_tipo' => 'R',
            'loc_zona' => 'U',
            'loc_endereco' => $this->faker->streetName(),
            'loc_numero' => $this->faker->numberBetween(1, 2000),
            'loc_bairro' => $this->faker->city(),
            'loc_cidade' => 'Cidade Teste',
            'loc_estado' => 'RS',
            'loc_pais' => 'Brasil',
            'loc_latitude' => '-28.8000000',
            'loc_longitude' => '-52.5000000',
            'loc_codigo_unico' => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
        ];
    }
}
