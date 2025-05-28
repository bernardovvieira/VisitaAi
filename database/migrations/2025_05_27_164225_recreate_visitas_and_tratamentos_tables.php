<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Remove a FK de monitoradas para poder recriar visitas
        Schema::table('monitoradas', function (Blueprint $table) {
            $table->dropForeign(['fk_visita_id']);
        });

        Schema::dropIfExists('tratamentos');
        Schema::dropIfExists('visitas');

        Schema::create('visitas', function (Blueprint $table) {
            $table->bigIncrements('vis_id');

            // Relacionamentos
            $table->unsignedBigInteger('fk_usuario_id');
            $table->unsignedBigInteger('fk_local_id');

            // Informações de controle
            $table->date('vis_data');
            $table->enum('vis_atividade', ['1','2','3','4','5','6','7','8'])->nullable();
            $table->enum('vis_visita_tipo', ['N', 'R'])->nullable(); // Normal / Recuperação
            $table->json('vis_pendencias')->nullable(); // A1, A2, B...

            // Depósitos inspecionados
            $table->integer('insp_a1')->nullable();
            $table->integer('insp_a2')->nullable();
            $table->integer('insp_b')->nullable();
            $table->integer('insp_c')->nullable();
            $table->integer('insp_d1')->nullable();
            $table->integer('insp_d2')->nullable();
            $table->integer('insp_e')->nullable();

            // Coleta de amostra
            $table->boolean('vis_coleta_amostra')->default(false);
            $table->integer('vis_qtd_tubitos')->nullable();
            $table->integer('vis_insp_inicial')->nullable();
            $table->integer('vis_insp_final')->nullable();

            // Tratamento e eliminação
            $table->integer('vis_depositos_eliminados')->nullable();
            $table->integer('vis_imoveis_tratados')->nullable();

            // Observações e estado
            $table->text('vis_observacoes')->nullable();
            $table->boolean('vis_concluida')->default(false);

            $table->timestamps();

            // Foreign keys
            $table->foreign('fk_usuario_id')->references('use_id')->on('users')->onDelete('cascade');
            $table->foreign('fk_local_id')->references('loc_id')->on('locais')->onDelete('cascade');
        });

        Schema::create('tratamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_visita_id');
            $table->enum('trat_tipo', ['larvicida', 'adulticida']);
            $table->enum('trat_forma', ['focal', 'perifocal']);
            $table->integer('linha')->nullable(); // linha 1, 2, etc.
            $table->string('produto')->nullable();
            $table->integer('qtd_gramas')->nullable();
            $table->integer('qtd_depositos_tratados')->nullable();
            $table->integer('qtd_cargas')->nullable();
            $table->timestamps();

            $table->foreign('fk_visita_id')->references('vis_id')->on('visitas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tratamentos');
        Schema::dropIfExists('visitas');
    }
};