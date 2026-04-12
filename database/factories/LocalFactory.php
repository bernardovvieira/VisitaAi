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

    /**
     * Base de enderecos de Soledade/RS para dados de seed e testes.
     *
     * @var array<int, array{endereco:string,bairro:string,cep:string}>
     */
    private const ENDERECOS_SOLEDADE = [
        ['endereco' => 'Rua Venancio Aires', 'bairro' => 'Centro', 'cep' => '99300-000'],
        ['endereco' => 'Rua 7 de Setembro', 'bairro' => 'Farroupilha', 'cep' => '99300-000'],
        ['endereco' => 'Avenida Marechal Floriano Peixoto', 'bairro' => 'Botucarai', 'cep' => '99300-000'],
        ['endereco' => 'Rua General Osorio', 'bairro' => 'Centro', 'cep' => '99300-000'],
        ['endereco' => 'Rua Coronel Falcon', 'bairro' => 'Missoes', 'cep' => '99300-000'],
        ['endereco' => 'Rua Bento Goncalves', 'bairro' => 'Expedicionario', 'cep' => '99300-000'],
        ['endereco' => 'Rua Benjamin Constant', 'bairro' => 'Ipiranga', 'cep' => '99300-000'],
        ['endereco' => 'Rua Mauricio Cardoso', 'bairro' => 'Fontes', 'cep' => '99300-000'],
    ];

    public function definition(): array
    {
        $endereco = $this->faker->randomElement(self::ENDERECOS_SOLEDADE);

        return [
            'loc_cep' => $endereco['cep'],
            'loc_tipo' => 'R',
            'loc_zona' => 'U',
            'loc_endereco' => $endereco['endereco'],
            'loc_numero' => $this->faker->numberBetween(1, 2000),
            'loc_bairro' => $endereco['bairro'],
            'loc_cidade' => 'Soledade',
            'loc_estado' => 'RS',
            'loc_pais' => 'Brasil',
            'loc_latitude' => number_format(-28.8353 + mt_rand(-700, 700) / 10000, 7, '.', ''),
            'loc_longitude' => number_format(-52.5081 + mt_rand(-700, 700) / 10000, 7, '.', ''),
            'loc_codigo_unico' => (string) $this->faker->unique()->numberBetween(10000000, 99999999),
        ];
    }
}
