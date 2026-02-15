<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

/**
 * Seed da instância base (visitaai.cloud, ibirapuita.visitaai.cloud, etc.).
 * Adiciona apenas o usuário administrador gestor.
 */
class AdminBaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'use_nome'         => 'Bernardo Vivian Vieira',
            'use_cpf'          => '054.023.910-09',
            'use_email'        => 'bernardo@bitwise.dev.br',
            'use_senha'        => bcrypt('Melancia@13?'),
            'email_verified_at'=> now(),
            'use_perfil'       => 'gestor',
            'use_aprovado'     => true,
            'use_data_criacao' => now(),
        ]);
    }
}
