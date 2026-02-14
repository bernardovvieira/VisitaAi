<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('use_id');
            $table->string('use_nome');
            $table->string('use_cpf')->unique();
            $table->string('use_email')->unique();
            $table->string('use_senha');
            $table->string('remember_token', 100)->nullable();
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->enum('use_perfil', ['gestor', 'agente_endemias', 'agente_saude']);
            $table->boolean('use_aprovado')->default(false);
            $table->string('use_tema', 10)->default('light');
            $table->timestamp('use_data_criacao')->useCurrent();
            $table->date('use_data_anonimizacao')->nullable();
            $table->unsignedBigInteger('fk_gestor_id')->nullable();

            $table->foreign('fk_gestor_id')
                ->references('use_id')->on('users')
                ->onDelete('set null');
        });

        // Coluna login = use_email (Fortify/guard usam WHERE login = ?)
        DB::statement('ALTER TABLE users ADD COLUMN login VARCHAR(255) GENERATED ALWAYS AS (use_email) STORED NOT NULL');
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('users');
        Schema::enableForeignKeyConstraints();
    }
};
