<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->string('log_ip', 45)->nullable()->after('log_data')->comment('IP do dispositivo que executou a ação');
            $table->text('log_user_agent')->nullable()->after('log_ip')->comment('User-Agent do navegador/dispositivo');
        });
    }

    public function down(): void
    {
        Schema::table('logs', function (Blueprint $table) {
            $table->dropColumn(['log_ip', 'log_user_agent']);
        });
    }
};
