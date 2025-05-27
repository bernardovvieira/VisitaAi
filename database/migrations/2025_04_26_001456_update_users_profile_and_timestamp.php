<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1) Garante que use_perfil seja ENUM('gestor','agente_endemias','agente_saude')
            $table->enum('use_perfil', ['gestor', 'agente_endemias', 'agente_saude'])
                  ->change();

            // 2) Converte use_data_criacao para TIMESTAMP com CURRENT_TIMESTAMP
            $table->timestamp('use_data_criacao')
                  ->useCurrent()
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 1) Reverte use_perfil para string
            $table->string('use_perfil', 255)
                  ->change();

            // 2) Reverte use_data_criacao para DATE com default fixo
            $table->date('use_data_criacao')
                  ->default('2025-04-17')
                  ->change();
        });
    }
};
