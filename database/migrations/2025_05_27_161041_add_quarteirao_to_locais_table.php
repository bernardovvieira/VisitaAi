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
        Schema::table('locais', function (Blueprint $table) {
            $table->string('loc_quarteirao', 10)->nullable()->after('loc_tipo');
            $table->string('loc_complemento', 50)->nullable()->after('loc_quarteirao');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn('loc_quarteirao');
        });
    }
};
