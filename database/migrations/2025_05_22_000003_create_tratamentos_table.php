<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tratamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fk_visita_id');
            $table->enum('trat_tipo', ['Larvicida', 'Adulticida']);
            $table->enum('trat_forma', ['Focal', 'Perifocal']);
            $table->integer('linha')->nullable();
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
    }
};
