<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('logs')) {
            return;
        }
        Schema::create('logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('log_user_id');
            $table->string('log_acao');
            $table->string('log_entidade');
            $table->string('log_tipo');
            $table->text('log_descricao')->nullable();
            $table->timestamp('log_data')->useCurrent();

            $table->foreign('log_user_id')->references('use_id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};
