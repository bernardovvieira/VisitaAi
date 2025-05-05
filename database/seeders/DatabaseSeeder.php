<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'use_nome' => 'Teste',
            'use_cpf' => '111.111.111-11',
            'use_email' => 'teste@exemplo.com',
            'use_senha' => bcrypt('senha123'),
            'use_perfil' => 'gestor',
            'use_aprovado' => true,
            'use_data_criacao' => now(),
        ]);
    }
}
