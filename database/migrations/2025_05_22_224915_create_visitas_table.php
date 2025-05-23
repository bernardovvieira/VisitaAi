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
        Schema::create('visitas', function (Blueprint $table) {
            $table->bigIncrements('vis_id');
            $table->date('vis_data');
            $table->text('vis_observacoes')->nullable();
            $table->unsignedBigInteger('fk_local_id');
            $table->unsignedBigInteger('fk_usuario_id');
            $table->unsignedBigInteger('fk_doenca_id');
            $table->timestamps();

            $table->foreign('fk_local_id')->references('loc_id')->on('locais');
            $table->foreign('fk_usuario_id')->references('use_id')->on('users');
            $table->foreign('fk_doenca_id')->references('doe_id')->on('doencas');
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
