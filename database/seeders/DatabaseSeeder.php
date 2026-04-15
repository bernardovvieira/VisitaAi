<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed completo para ambiente de testes/demo sem alterar logins existentes.
     */
    public function run(): void
    {
        $this->call(FullSystemTestSeeder::class);
    }
}
