<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveFkDoencaIdFromVisitasTable extends Migration
{
    public function up()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->dropForeign(['fk_doenca_id']);
            $table->dropColumn('fk_doenca_id');
        });
    }

    public function down()
    {
        Schema::table('visitas', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_doenca_id');
            $table->foreign('fk_doenca_id')->references('doe_id')->on('doencas');
        });
    }
}