<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->bigIncrements('vis_id');
            $table->unsignedBigInteger('fk_usuario_id');
            $table->unsignedBigInteger('fk_local_id');
            $table->date('vis_data');
            $table->string('vis_ciclo', 10)->nullable();
            $table->enum('vis_atividade', ['1','2','3','4','5','6','7','8'])->nullable();
            $table->enum('vis_visita_tipo', ['N', 'R'])->nullable();
            $table->boolean('vis_pendencias')->default(false);
            $table->integer('insp_a1')->nullable();
            $table->integer('insp_a2')->nullable();
            $table->integer('insp_b')->nullable();
            $table->integer('insp_c')->nullable();
            $table->integer('insp_d1')->nullable();
            $table->integer('insp_d2')->nullable();
            $table->integer('insp_e')->nullable();
            $table->boolean('vis_coleta_amostra')->default(false);
            $table->integer('vis_qtd_tubitos')->nullable();
            $table->integer('vis_amos_inicial')->nullable();
            $table->integer('vis_amos_final')->nullable();
            $table->integer('vis_depositos_eliminados')->nullable();
            $table->integer('vis_imoveis_tratados')->nullable();
            $table->text('vis_observacoes')->nullable();
            $table->boolean('vis_concluida')->default(false);
            $table->timestamps();

            $table->foreign('fk_usuario_id')->references('use_id')->on('users')->onDelete('cascade');
            $table->foreign('fk_local_id')->references('loc_id')->on('locais')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
