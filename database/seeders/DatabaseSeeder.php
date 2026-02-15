<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed da instância demo (demo.visitaai.cloud).
     * Para instância base (visitaai.cloud), use: php artisan db:seed --class=AdminBaseSeeder
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}