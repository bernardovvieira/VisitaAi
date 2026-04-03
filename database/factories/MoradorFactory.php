<?php

namespace Database\Factories;

use App\Models\Local;
use App\Models\Morador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Morador>
 */
class MoradorFactory extends Factory
{
    protected $model = Morador::class;

    public function definition(): array
    {
        return [
            'fk_local_id' => Local::factory(),
            'mor_nome' => $this->faker->name(),
            'mor_data_nascimento' => $this->faker->dateTimeBetween('-75 years', '-2 years'),
            'mor_escolaridade' => 'nao_informado',
            'mor_renda_faixa' => 'nao_informado',
            'mor_observacao' => null,
        ];
    }
}
