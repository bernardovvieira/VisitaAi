<?php

namespace Database\Seeders;

use App\Models\Doenca;
use App\Models\Local;
use App\Models\Morador;
use App\Models\User;
use App\Models\Visita;
use Illuminate\Database\Seeder;

/**
 * Seed da instância demo (demo.visitaai.cloud).
 * Usuários de teste, doenças, locais e visitas de exemplo.
 */
class DemoSeeder extends Seeder
{
    private const BAIRRO_CENTROS = [
        'Farroupilha' => ['lat' => -28.8347, 'lng' => -52.5073],
        'Centro' => ['lat' => -28.8286, 'lng' => -52.5096],
        'Botucaraí' => ['lat' => -28.8120, 'lng' => -52.5079],
    ];

    public function run(): void
    {
        // Usuários (Gestor e profissionais ACE/ACS)
        $usuarios = [
            [
                'use_nome' => 'Gestor Teste',
                'use_cpf' => '444.444.444-44',
                'use_email' => 'gestor@exemplo.com',
                'use_senha' => bcrypt('Senha123!'),
                'use_perfil' => 'gestor',
                'use_aprovado' => true,
                'use_data_criacao' => now(),
            ],
            [
                'use_nome' => 'Agente Um',
                'use_cpf' => '111.111.111-11',
                'use_email' => 'agente1@exemplo.com',
                'use_senha' => bcrypt('Senha123!'),
                'use_perfil' => 'agente_endemias',
                'use_aprovado' => true,
                'use_data_criacao' => now(),
            ],
            [
                'use_nome' => 'Agente Dois',
                'use_cpf' => '222.222.222-22',
                'use_email' => 'agente2@exemplo.com',
                'use_senha' => bcrypt('Senha123!'),
                'use_perfil' => 'agente_saude',
                'use_aprovado' => true,
                'use_data_criacao' => now(),
            ],
            [
                'use_nome' => 'Agente Três',
                'use_cpf' => '333.333.333-33',
                'use_email' => 'agente3@exemplo.com',
                'use_senha' => bcrypt('Senha123!'),
                'use_perfil' => 'agente_endemias',
                'use_aprovado' => false,
                'use_data_criacao' => now(),
            ],
        ];

        foreach ($usuarios as $usuario) {
            User::factory()->create($usuario);
        }

        // Doença padrão da demo: apenas Dengue (arbovirose prioritária)
        $dengue = [
            'doe_nome' => 'Dengue',
            'doe_sintomas' => [
                'Febre alta', 'Dor de cabeça intensa', 'Dor atrás dos olhos',
                'Dor muscular e nas articulações', 'Náusea', 'Vômito', 'Manchas na pele',
            ],
            'doe_transmissao' => ['Picada do mosquito Aedes aegypti'],
            'doe_medidas_controle' => [
                'Eliminação de criadouros', 'Uso de repelentes', 'Telagem de recipientes',
                'Educação em saúde', 'Ações de controle vetorial',
            ],
        ];
        Doenca::create($dengue);

        // Locais
        $locais = [
            [
                'loc_codigo_unico' => '85121055',
                'loc_tipo' => 'T',
                'loc_zona' => 'U',
                'loc_quarteirao' => 6,
                'loc_sequencia' => 2,
                'loc_lado' => 1,
                'loc_codigo' => 00022,
                'loc_categoria' => 'BIR',
                'loc_cep' => '99300-000',
                'loc_endereco' => 'Rua 7 de Setembro',
                'loc_numero' => '404',
                'loc_bairro' => 'Farroupilha',
                'loc_cidade' => 'Soledade',
                'loc_estado' => 'RS',
                'loc_pais' => 'Brasil',
                'loc_latitude' => (string) self::BAIRRO_CENTROS['Farroupilha']['lat'],
                'loc_longitude' => (string) self::BAIRRO_CENTROS['Farroupilha']['lng'],
            ],
            [
                'loc_codigo_unico' => '25164321',
                'loc_tipo' => 'R',
                'loc_zona' => 'U',
                'loc_complemento' => 'Casa',
                'loc_quarteirao' => 3,
                'loc_sequencia' => 4,
                'loc_lado' => 2,
                'loc_codigo' => 00022,
                'loc_categoria' => 'BIR',
                'loc_cep' => '99300-000',
                'loc_endereco' => 'Rua Venâncio Aires',
                'loc_numero' => '947',
                'loc_bairro' => 'Centro',
                'loc_cidade' => 'Soledade',
                'loc_estado' => 'RS',
                'loc_pais' => 'Brasil',
                'loc_latitude' => (string) self::BAIRRO_CENTROS['Centro']['lat'],
                'loc_longitude' => (string) self::BAIRRO_CENTROS['Centro']['lng'],
                'loc_responsavel_nome' => 'Bernardo Vivian Vieira',
            ],
            [
                'loc_codigo_unico' => '12345678',
                'loc_tipo' => 'C',
                'loc_zona' => 'U',
                'loc_complemento' => 'Universidade de Passo Fundo',
                'loc_quarteirao' => 2,
                'loc_sequencia' => 2,
                'loc_lado' => 4,
                'loc_codigo' => 00022,
                'loc_categoria' => 'BIR',
                'loc_cep' => '99300-000',
                'loc_endereco' => 'Avenida Marechal Floriano Peixoto',
                'loc_numero' => '3033',
                'loc_bairro' => 'Botucaraí',
                'loc_cidade' => 'Soledade',
                'loc_estado' => 'RS',
                'loc_pais' => 'Brasil',
                'loc_latitude' => (string) self::BAIRRO_CENTROS['Botucaraí']['lat'],
                'loc_longitude' => (string) self::BAIRRO_CENTROS['Botucaraí']['lng'],
                'loc_responsavel_nome' => null,
            ],
        ];

        foreach ($locais as $local) {
            Local::create($local);
        }

        $localCentro = Local::where('loc_codigo_unico', '25164321')->first();
        if ($localCentro) {
            Morador::create([
                'fk_local_id' => $localCentro->loc_id,
                'mor_nome' => 'Morador Exemplo',
                'mor_data_nascimento' => '1988-03-15',
                'mor_escolaridade' => 'medio_completo',
                'mor_renda_faixa' => 'ate_2_sm',
            ]);
            Morador::create([
                'fk_local_id' => $localCentro->loc_id,
                'mor_nome' => null,
                'mor_data_nascimento' => '2012-08-01',
                'mor_escolaridade' => 'fundamental_incompleto',
                'mor_renda_faixa' => null,
            ]);
            for ($i = 0; $i < 3; $i++) {
                Morador::create([
                    'fk_local_id' => $localCentro->loc_id,
                    'mor_nome' => 'Ocupante Demo Centro '.($i + 1),
                    'mor_data_nascimento' => now()->subYears(25 + $i)->format('Y-m-d'),
                    'mor_escolaridade' => 'superior_completo',
                    'mor_renda_faixa' => 'ate_1_sm',
                ]);
            }
        }

        $localFarroupilha = Local::where('loc_codigo_unico', '85121055')->first();
        if ($localFarroupilha) {
            for ($i = 0; $i < 5; $i++) {
                Morador::create([
                    'fk_local_id' => $localFarroupilha->loc_id,
                    'mor_nome' => 'Ocupante Demo Farroupilha '.($i + 1),
                    'mor_data_nascimento' => now()->subYears(8 + $i * 7)->format('Y-m-d'),
                    'mor_escolaridade' => $i % 2 === 0 ? 'medio_completo' : 'fundamental_completo',
                    'mor_renda_faixa' => 'ate_3_sm',
                ]);
            }
        }

        $localBotucarai = Local::where('loc_codigo_unico', '12345678')->first();
        if ($localBotucarai) {
            for ($i = 0; $i < 5; $i++) {
                Morador::create([
                    'fk_local_id' => $localBotucarai->loc_id,
                    'mor_nome' => 'Ocupante Demo Botucaraí '.($i + 1),
                    'mor_data_nascimento' => $i === 0 ? null : now()->subYears(45 + $i)->format('Y-m-d'),
                    'mor_escolaridade' => 'nao_informado',
                    'mor_renda_faixa' => 'nao_informado',
                ]);
            }
        }

        // Visitas e Tratamentos (fk_usuario_id => 2 = Agente Um)
        $visita1 = Visita::create([
            'fk_usuario_id' => 2,
            'fk_local_id' => 2,
            'vis_data' => '2025-05-30',
            'vis_ciclo' => '05/25',
            'vis_atividade' => '2',
            'vis_pendencias' => true,
            'insp_b' => 1,
            'vis_coleta_amostra' => false,
            'vis_concluida' => false,
            'vis_observacoes' => 'Inspeção de rotina com pendências a serem resolvidas.',
        ]);

        $visita2 = Visita::create([
            'fk_usuario_id' => 2,
            'fk_local_id' => 3,
            'vis_data' => '2025-05-29',
            'vis_ciclo' => '05/25',
            'vis_atividade' => '7',
            'vis_pendencias' => false,
            'vis_coleta_amostra' => true,
            'vis_concluida' => true,
            'vis_amos_inicial' => 100,
            'vis_amos_final' => 103,
            'vis_qtd_tubitos' => 3,
            'vis_observacoes' => 'Coleta de amostra em terreno baldio com presença de larvas.',
            'insp_a1' => 2,
            'insp_a2' => 1,
            'vis_depositos_eliminados' => 1,
        ]);

        $visita2->tratamentos()->createMany([
            [
                'trat_tipo' => 'Larvicida',
                'trat_forma' => 'Focal',
                'linha' => 1,
                'qtd_gramas' => 2,
                'qtd_depositos_tratados' => 2,
            ],
            [
                'trat_tipo' => 'Adulticida',
                'trat_forma' => 'Perifocal',
                'qtd_cargas' => 3,
            ],
        ]);

        $visita2->doencas()->sync([Doenca::where('doe_nome', 'Dengue')->value('doe_id')]);
    }
}
