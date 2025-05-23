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
        Schema::create('locais', function (Blueprint $table) {
            $table->bigIncrements('loc_id');
            $table->string('loc_endereco');
            $table->string('loc_bairro');
            $table->string('loc_coordenadas'); // armazenar como texto tipo "lat,long"
            $table->string('loc_codigo_unico')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locais');
    }
};
