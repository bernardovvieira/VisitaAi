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
        Schema::create('monitoradas', function (Blueprint $table) {
            $table->bigIncrements('mon_id');
            $table->unsignedBigInteger('fk_visita_id');
            $table->unsignedBigInteger('fk_doenca_id');
            $table->timestamps();

            $table->foreign('fk_visita_id')->references('vis_id')->on('visitas')->onDelete('cascade');
            $table->foreign('fk_doenca_id')->references('doe_id')->on('doencas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monitoradas');
    }
};
