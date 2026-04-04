<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            if (! Schema::hasColumn('moradores', 'mor_cor_raca')) {
                $table->string('mor_cor_raca', 30)->nullable()->after('mor_renda_faixa');
            }
            if (! Schema::hasColumn('moradores', 'mor_situacao_trabalho')) {
                $table->string('mor_situacao_trabalho', 40)->nullable()->after('mor_cor_raca');
            }
        });
    }

    public function down(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            if (Schema::hasColumn('moradores', 'mor_situacao_trabalho')) {
                $table->dropColumn('mor_situacao_trabalho');
            }
            if (Schema::hasColumn('moradores', 'mor_cor_raca')) {
                $table->dropColumn('mor_cor_raca');
            }
        });
    }
};
