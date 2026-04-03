<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('moradores')) {
            return;
        }

        Schema::create('moradores', function (Blueprint $table) {
            $table->bigIncrements('mor_id');
            $table->unsignedBigInteger('fk_local_id');
            $table->string('mor_nome', 255)->nullable();
            $table->date('mor_data_nascimento')->nullable();
            $table->string('mor_escolaridade', 50)->nullable();
            $table->string('mor_renda_faixa', 50)->nullable();
            $table->text('mor_observacao')->nullable();
            $table->timestamps();

            $table->foreign('fk_local_id')->references('loc_id')->on('locais')->onDelete('cascade');
            $table->index('fk_local_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moradores');
    }
};
