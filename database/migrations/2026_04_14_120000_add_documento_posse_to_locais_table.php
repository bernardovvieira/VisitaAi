<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            if (! Schema::hasColumn('locais', 'loc_documento_posse_path')) {
                $table->string('loc_documento_posse_path', 255)->nullable()->after('loc_responsavel_nome');
            }
            if (! Schema::hasColumn('locais', 'loc_documento_posse_nome')) {
                $table->string('loc_documento_posse_nome', 255)->nullable()->after('loc_documento_posse_path');
            }
            if (! Schema::hasColumn('locais', 'loc_documento_posse_mime')) {
                $table->string('loc_documento_posse_mime', 120)->nullable()->after('loc_documento_posse_nome');
            }
            if (! Schema::hasColumn('locais', 'loc_documento_posse_tamanho')) {
                $table->unsignedBigInteger('loc_documento_posse_tamanho')->nullable()->after('loc_documento_posse_mime');
            }
        });
    }

    public function down(): void
    {
        Schema::table('locais', function (Blueprint $table) {
            $table->dropColumn([
                'loc_documento_posse_tamanho',
                'loc_documento_posse_mime',
                'loc_documento_posse_nome',
                'loc_documento_posse_path',
            ]);
        });
    }
};
