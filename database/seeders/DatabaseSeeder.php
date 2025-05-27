<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doenca;
use App\Models\Local;
use App\Models\Visita;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Usuários (Gestor e Agentes)
        $usuarios = [
            [
                'use_nome'        => 'Gestor Teste',
                'use_cpf'         => '444.444.444-44',
                'use_email'       => 'gestor@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'gestor',
                'use_aprovado'    => true,
                'use_data_criacao'=> now(),
            ],
            [
                'use_nome'        => 'Agente Um',
                'use_cpf'         => '111.111.111-11',
                'use_email'       => 'agente1@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'agente_endemias',
                'use_aprovado'    => true,
                'use_data_criacao'=> now(),
            ],
            [
                'use_nome'        => 'Agente Dois',
                'use_cpf'         => '222.222.222-22',
                'use_email'       => 'agente2@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'agente_saude',
                'use_aprovado'    => true,
                'use_data_criacao'=> now(),
            ],
            [
                'use_nome'        => 'Agente Três',
                'use_cpf'         => '333.333.333-33',
                'use_email'       => 'agente3@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'agente_endemias',
                'use_aprovado'    => false,
                'use_data_criacao'=> now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::factory()->create($usuario);
        }

        // Doenças monitoradas (prioridade: arboviroses)
        $doencas = [
            [
                'doe_nome' => 'Dengue',
                'doe_sintomas' => [
                    'Febre alta', 'Dor de cabeça intensa', 'Dor atrás dos olhos',
                    'Dor muscular e nas articulações', 'Náusea', 'Vômito', 'Manchas na pele'
                ],
                'doe_transmissao' => ['Picada do mosquito Aedes aegypti'],
                'doe_medidas_controle' => [
                    'Eliminação de criadouros', 'Uso de repelentes', 'Telagem de recipientes',
                    'Educação em saúde', 'Ações de controle vetorial'
                ],
            ],
            [
                'doe_nome' => 'Zika',
                'doe_sintomas' => [
                    'Febre baixa', 'Conjuntivite', 'Dor nas articulações',
                    'Manchas vermelhas na pele', 'Dor de cabeça', 'Coceira'
                ],
                'doe_transmissao' => [
                    'Picada do mosquito Aedes aegypti',
                    'Transmissão sexual', 'Transmissão vertical (gestante para o feto)'
                ],
                'doe_medidas_controle' => [
                    'Eliminação de criadouros', 'Uso de preservativos',
                    'Proteção da gestante', 'Controle vetorial', 'Repelente'
                ],
            ],
            [
                'doe_nome' => 'Chikungunya',
                'doe_sintomas' => [
                    'Febre alta', 'Dor intensa nas articulações', 'Dor muscular',
                    'Cefaleia', 'Manchas vermelhas na pele', 'Fadiga'
                ],
                'doe_transmissao' => ['Picada do mosquito Aedes aegypti'],
                'doe_medidas_controle' => [
                    'Controle de vetores', 'Repelente', 'Acompanhamento médico',
                    'Eliminação de água parada', 'Campanhas de conscientização'
                ],
            ],
        ];

        foreach ($doencas as $doenca) {
            Doenca::create($doenca);
        }

        // Locais
        $locais = [
            [
                'loc_codigo_unico' => '85121055',
                'loc_tipo'          => 'T', // T = Terreno Baldio
                'loc_quarteirao'   => '6',
                'loc_cep'          => '99300-000',
                'loc_endereco'     => 'Rua 7 de Setembro',
                'loc_numero'       => '404',
                'loc_bairro'       => 'Farroupilha',
                'loc_cidade'       => 'Soledade',
                'loc_estado'       => 'RS',
                'loc_pais'         => 'Brasil',
                'loc_latitude'     => '-28.8353131',
                'loc_longitude'    => '-52.5081682',
            ],
            [
                'loc_codigo_unico' => '25164321',
                'loc_tipo'          => 'R', // R = Residencial
                'loc_quarteirao'   => '3',
                'loc_cep'          => '99300-000',
                'loc_endereco'     => 'Rua Venâncio Aires',
                'loc_numero'       => '947',
                'loc_bairro'       => 'Centro',
                'loc_cidade'       => 'Soledade',
                'loc_estado'       => 'RS',
                'loc_pais'         => 'Brasil',
                'loc_latitude'     => '-28.8283392',
                'loc_longitude'    => '-52.5098634',
            ],
            [
                'loc_codigo_unico' => '12345678',
                'loc_tipo'          => 'C', // C = Comercial
                'loc_quarteirao'   => '2',
                'loc_cep'          => '99300-000',
                'loc_endereco'     => 'Avenida Marechal Floriano Peixoto',
                'loc_numero'       => '3033',
                'loc_bairro'       => 'Botucaraí',
                'loc_cidade'       => 'Soledade',
                'loc_estado'       => 'RS',
                'loc_pais'         => 'Brasil',
                'loc_latitude'     => '-28.8109116',
                'loc_longitude'    => '-52.5078464',
            ]
        ];

        foreach ($locais as $local) {
            Local::create($local);
        }

        // Visitas
        $visita1 = Visita::create([
            'vis_data' => now()->subDays(1)->toDateString(),
            'vis_observacoes' => 'Primeira visita de teste',
            'vis_tipo' => 'LI+T',
            'fk_local_id' => 1,
            'fk_usuario_id' => 2, // Agente Um (agente_endemias)
        ]);
        $visita1->doencas()->attach([1, 2]); // Dengue, Zika

        $visita2 = Visita::create([
            'vis_data' => now()->toDateString(),
            'vis_observacoes' => 'Segunda visita de teste',
            'vis_tipo' => 'LI+T',
            'fk_local_id' => 2,
            'fk_usuario_id' => 2, // Agente Um (agente_endemias)
        ]);
        $visita2->doencas()->attach([3]); // Chikungunya

        $visita3 = Visita::create([
            'vis_data' => now()->subDays(2)->toDateString(),
            'vis_observacoes' => 'Visita de acompanhamento',
            'vis_tipo' => 'LIRAa',
            'fk_local_id' => 3,
            'fk_usuario_id' => 3, // Agente Dois (agente_saude)
        ]);
        $visita3->doencas()->attach([1]); // Dengue
    }
}