<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('locais')) {
            return;
        }
        Schema::create('locais', function (Blueprint $table) {
            $table->bigIncrements('loc_id');
            $table->string('loc_cep', 9);
            $table->char('loc_tipo', 1)->default('R');
            $table->string('loc_quarteirao', 10)->nullable();
            $table->string('loc_complemento', 50)->nullable();
            $table->string('loc_zona');
            $table->string('loc_categoria')->nullable();
            $table->integer('loc_sequencia')->nullable();
            $table->integer('loc_lado')->nullable();
            $table->string('loc_codigo')->nullable();
            $table->string('loc_endereco', 255);
            $table->integer('loc_numero')->nullable();
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

    public function down(): void
    {
        Schema::dropIfExists('locais');
    }
};
