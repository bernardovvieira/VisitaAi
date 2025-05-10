<?php

namespace Database\Factories;

use App\Models\Doenca;
use Illuminate\Database\Eloquent\Factories\Factory;

class DoencaFactory extends Factory
{
    protected $model = Doenca::class;

    public function definition()
    {
        return [
            'doe_nome'             => $this->faker->unique()->word(),
            'doe_sintomas'         => $this->faker->words(3),
            'doe_transmissao'      => $this->faker->words(2),
            'doe_medidas_controle' => $this->faker->words(2),
        ];
    }
}
