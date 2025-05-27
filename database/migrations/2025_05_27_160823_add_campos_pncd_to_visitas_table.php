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
        Schema::table('visitas', function (Blueprint $table) {
            $table->enum('vis_visita_tipo', ['N', 'R'])->nullable();
            $table->json('vis_pendencias')->nullable();
            $table->integer('vis_insp_inicial')->nullable();
            $table->integer('vis_insp_final')->nullable();
            $table->boolean('vis_coleta_amostra')->default(false);
            $table->integer('vis_qtd_tubitos')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn([
                'vis_visita_tipo',
                'vis_pendencias',
                'vis_insp_inicial',
                'vis_insp_final',
                'vis_coleta_amostra',
                'vis_qtd_tubitos',
            ]);
        });
    }
};
