<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            // PK personalizado
            $table->bigIncrements('use_id');

            // Campos conforme diagrama
            $table->string('use_nome'); // Nome completo
            $table->string('use_cpf')->unique();
            $table->string('use_email')->unique();
            $table->string('use_senha');
            $table->string('use_perfil'); // 'agente' ou 'gestor'
            $table->date('use_data_criacao')->default(now());
            $table->date('use_data_anonimizacao')->nullable();

            // FK para o gestor (relacionamento 1:N)
            $table->unsignedBigInteger('fk_gestor_id')->nullable();
            $table->foreign('fk_gestor_id')
                  ->references('use_id')->on('users')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
