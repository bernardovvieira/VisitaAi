<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('doencas', function (Blueprint $table) {
            $table->bigIncrements('doe_id');
            $table->string('doe_nome');
            $table->text('doe_sintomas');
            $table->text('doe_transmissao');
            $table->text('doe_medidas_controle');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doencas');
    }
};