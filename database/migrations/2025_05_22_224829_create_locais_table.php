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
            $table->string('loc_cep', 9);
            $table->string('loc_endereco', 255);
            $table->string('loc_numero', 20);
            $table->string('loc_bairro', 100);
            $table->string('loc_cidade', 100)->nullable();
            $table->string('loc_estado', 2)->nullable();
            $table->string('loc_pais', 100)->default('Brasil');
            $table->string('loc_latitude', 20);
            $table->string('loc_longitude', 20);
            $table->string('loc_codigo_unico', 255)->unique();
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
