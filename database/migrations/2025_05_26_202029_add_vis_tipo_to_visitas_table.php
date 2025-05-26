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
        Schema::table('visitas', function (Blueprint $table) {
            $table->string('vis_tipo')->nullable()->after('vis_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropColumn('vis_tipo');
        });
    }
};
