<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            if (! Schema::hasColumn('moradores', 'mor_documento_pessoal_path')) {
                $table->string('mor_documento_pessoal_path', 255)->nullable()->after('mor_cpf');
            }
            if (! Schema::hasColumn('moradores', 'mor_documento_pessoal_nome')) {
                $table->string('mor_documento_pessoal_nome', 255)->nullable()->after('mor_documento_pessoal_path');
            }
            if (! Schema::hasColumn('moradores', 'mor_documento_pessoal_mime')) {
                $table->string('mor_documento_pessoal_mime', 120)->nullable()->after('mor_documento_pessoal_nome');
            }
            if (! Schema::hasColumn('moradores', 'mor_documento_pessoal_tamanho')) {
                $table->unsignedBigInteger('mor_documento_pessoal_tamanho')->nullable()->after('mor_documento_pessoal_mime');
            }
                if (! Schema::hasColumn('moradores', 'mor_rg_expedicao')) {
                    $table->date('mor_rg_expedicao')->nullable()->after('mor_rg_orgao');
                }
        });
    }

    public function down(): void
    {
        Schema::table('moradores', function (Blueprint $table) {
            foreach ([
                'mor_documento_pessoal_tamanho',
                'mor_documento_pessoal_mime',
                'mor_documento_pessoal_nome',
                'mor_documento_pessoal_path',
                    'mor_rg_expedicao',
            ] as $col) {
                if (Schema::hasColumn('moradores', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
