<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->string('loc_responsavel_nome', 255)->nullable()->after('loc_codigo_unico')->comment('Nome completo do responsável pelo imóvel (morador, locatário ou proprietário)');
        });
    }

    public function down(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn('loc_responsavel_nome');
        });
    }
};
