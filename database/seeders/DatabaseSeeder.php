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
                'use_perfil'      => 'agente',
                'use_aprovado'    => true,
                'use_data_criacao'=> now(),
            ],
            [
                'use_nome'        => 'Agente Dois',
                'use_cpf'         => '222.222.222-22',
                'use_email'       => 'agente2@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'agente',
                'use_aprovado'    => true,
                'use_data_criacao'=> now(),
            ],
            [
                'use_nome'        => 'Agente Três',
                'use_cpf'         => '333.333.333-33',
                'use_email'       => 'agente3@exemplo.com',
                'use_senha'       => bcrypt('senha123'),
                'use_perfil'      => 'agente',
                'use_aprovado'    => false,
                'use_data_criacao'=> now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::factory()->create($usuario);
        }

        // Doenças monitoradas
        $doencas = [
            [
                'doe_nome' => 'COVID-19',
                'doe_sintomas' => [
                    'Febre', 'Tosse seca', 'Fadiga', 'Dispneia', 'Anosmia', 'Ageusia',
                    'Congestão nasal', 'Dor de cabeça', 'Dor abdominal', 'Diarreia'
                ],
                'doe_transmissao' => [
                    'Gotículas respiratórias', 'Aerossois (transmissão aérea)', 'Fômites'
                ],
                'doe_medidas_controle' => [
                    'Higienização das mãos', 'Máscara cirúrgica', 'Máscara N95/FFP2',
                    'Ventilação adequada', 'Isolamento de casos', 'Vacinação'
                ],
            ],
            [
                'doe_nome' => 'Dengue',
                'doe_sintomas' => [
                    'Febre', 'Dor de cabeça', 'Mialgias', 'Artralgias', 'Rash cutâneo', 'Náusea'
                ],
                'doe_transmissao' => ['Vetor biológico'],
                'doe_medidas_controle' => [
                    'Controle de vetores', 'Educação em saúde', 'Vigilância epidemiológica'
                ],
            ],
            [
                'doe_nome' => 'Hepatite A',
                'doe_sintomas' => ['Febre', 'Náusea', 'Vômito', 'Dor abdominal', 'Diarreia'],
                'doe_transmissao' => [
                    'Água contaminada', 'Alimentos contaminados', 'Transmissão fecal-oral'
                ],
                'doe_medidas_controle' => [
                    'Higienização das mãos', 'WASH (água, saneamento e higiene)', 'Vacinação'
                ],
            ],
            [
                'doe_nome' => 'Influenza',
                'doe_sintomas' => [
                    'Febre', 'Tosse', 'Calafrios', 'Fadiga', 'Congestão nasal', 'Dor de cabeça'
                ],
                'doe_transmissao' => [
                    'Gotículas respiratórias', 'Fômites', 'Contato direto'
                ],
                'doe_medidas_controle' => [
                    'Máscara cirúrgica', 'Etiqueta respiratória', 'Vacinação',
                    'Distanciamento físico', 'Ventilação adequada'
                ],
            ],
        ];

        foreach ($doencas as $doenca) {
            Doenca::create($doenca);
        }

        // Locais
        $locais = [
            [
                'loc_codigo_unico' => '1000',
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
                'loc_codigo_unico' => '1001',
                'loc_cep'          => '99300-000',
                'loc_endereco'     => 'Rua Venâncio Aires',
                'loc_numero'       => '947',
                'loc_bairro'       => 'Centro',
                'loc_cidade'       => 'Soledade',
                'loc_estado'       => 'RS',
                'loc_pais'         => 'Brasil',
                'loc_latitude'     => '-28.8283392',
                'loc_longitude'    => '-52.5098634',
            ]
        ];

        foreach ($locais as $local) {
            Local::create($local);
        }

        // Visitas
        $visita1 = Visita::create([
            'vis_data' => now()->toDateString(),
            'vis_observacoes' => 'Primeira visita de teste',
            'fk_local_id' => 1,
            'fk_usuario_id' => 2,
        ]);
        $visita1->doencas()->attach([2, 4]); // Dengue, Influenza

        $visita2 = Visita::create([
            'vis_data' => now()->toDateString(),
            'vis_observacoes' => 'Segunda visita de teste',
            'fk_local_id' => 2,
            'fk_usuario_id' => 3,
        ]);
        $visita2->doencas()->attach([1, 3]); // COVID-19, Hepatite A
    }
}