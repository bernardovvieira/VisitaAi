<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('log_user_id'); // ID do agente ou gestor
            $table->string('log_acao'); // ex: "Criação de visita"
            $table->string('log_entidade'); // ex: "Visita"
            $table->string('log_tipo'); // ex: "create", "update", "delete"
            $table->text('log_descricao')->nullable(); // texto descritivo da ação
            $table->timestamp('log_data')->useCurrent();

            $table->foreign('log_user_id')->references('use_id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
