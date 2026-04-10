<?php

namespace Database\Seeders;

use App\Models\Doenca;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seed da instância base (visitaai.cloud, ibirapuita.visitaai.cloud, etc.).
 * Adiciona o usuário administrador gestor e garante a doença Dengue na base.
 */
class AdminBaseSeeder extends Seeder
{
    public function run(): void
    {
        // Dengue sempre cadastrada no deploy (dados validados conforme MS/PNCD)
        Doenca::firstOrCreate(
            ['doe_nome' => 'Dengue'],
            [
                'doe_sintomas' => [
                    'Febre alta', 'Dor de cabeça intensa', 'Dor atrás dos olhos',
                    'Dor muscular e nas articulações', 'Náusea', 'Vômito', 'Manchas na pele',
                ],
                'doe_transmissao' => ['Picada do mosquito Aedes aegypti'],
                'doe_medidas_controle' => [
                    'Eliminação de criadouros', 'Uso de repelentes', 'Telagem de recipientes',
                    'Educação em saúde', 'Ações de controle vetorial',
                ],
            ]
        );

        User::create([
            'use_nome' => 'Bernardo Vivian Vieira',
            'use_cpf' => '054.023.910-09',
            'use_email' => 'bernardo@bitwise.dev.br',
            'use_senha' => bcrypt('Melancia@13?'),
            'email_verified_at' => now(),
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
            'use_data_criacao' => now(),
        ]);
    }
}
