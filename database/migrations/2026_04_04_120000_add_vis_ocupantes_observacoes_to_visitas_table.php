<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            if (! Schema::hasColumn('visitas', 'vis_ocupantes_observacoes')) {
                $table->json('vis_ocupantes_observacoes')->nullable()->after('vis_observacoes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('visitas', function (Blueprint $table) {
            if (Schema::hasColumn('visitas', 'vis_ocupantes_observacoes')) {
                $table->dropColumn('vis_ocupantes_observacoes');
            }
        });
    }
};
