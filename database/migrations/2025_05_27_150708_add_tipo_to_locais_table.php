<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->char('loc_tipo', 1)->default('R'); // R = Residencial
            $table->integer('loc_numero')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
        public function down()
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn('loc_tipo');
            $table->integer('loc_numero')->nullable(false)->change();
        });
    }
};
