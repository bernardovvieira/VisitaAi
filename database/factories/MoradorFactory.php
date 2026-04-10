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
            'mor_cor_raca' => 'nao_informado',
            'mor_situacao_trabalho' => 'nao_informado',
            'mor_sexo' => 'nao_informado',
            'mor_estado_civil' => 'nao_informado',
            'mor_naturalidade' => null,
            'mor_profissao' => null,
            'mor_parentesco' => 'nao_informado',
            'mor_referencia_familiar' => false,
            'mor_telefone' => null,
            'mor_rg_numero' => null,
            'mor_rg_orgao' => null,
            'mor_cpf' => null,
            'mor_tempo_uniao_conjuge' => null,
            'mor_ajuda_compra_imovel' => null,
            'mor_renda_formal_informal' => 'nao_informado',
            'mor_observacao' => null,
        ];
    }
}
