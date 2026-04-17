<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('local_documentos')) {
            Schema::create('local_documentos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fk_local_id');
                $table->string('path', 255);
                $table->string('original_name', 255)->nullable();
                $table->string('mime', 120)->nullable();
                $table->unsignedBigInteger('size_bytes')->nullable();
                $table->timestamps();

                $table->foreign('fk_local_id')->references('loc_id')->on('locais')->onDelete('cascade');
                $table->index('fk_local_id');
            });
        }

        if (! Schema::hasTable('morador_documentos')) {
            Schema::create('morador_documentos', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('fk_morador_id');
                $table->string('path', 255);
                $table->string('original_name', 255)->nullable();
                $table->string('mime', 120)->nullable();
                $table->unsignedBigInteger('size_bytes')->nullable();
                $table->timestamps();

                $table->foreign('fk_morador_id')->references('mor_id')->on('moradores')->onDelete('cascade');
                $table->index('fk_morador_id');
            });
        }

        $this->migrateLegacyLocalDocumentos();
        $this->migrateLegacyMoradorDocumentos();
    }

    private function migrateLegacyLocalDocumentos(): void
    {
        if (! Schema::hasColumn('locais', 'loc_documento_posse_path')) {
            return;
        }

        $rows = DB::table('locais')
            ->whereNotNull('loc_documento_posse_path')
            ->where('loc_documento_posse_path', '!=', '')
            ->get(['loc_id', 'loc_documento_posse_path', 'loc_documento_posse_nome', 'loc_documento_posse_mime', 'loc_documento_posse_tamanho']);

        foreach ($rows as $row) {
            DB::table('local_documentos')->insert([
                'fk_local_id' => $row->loc_id,
                'path' => $row->loc_documento_posse_path,
                'original_name' => $row->loc_documento_posse_nome,
                'mime' => $row->loc_documento_posse_mime,
                'size_bytes' => $row->loc_documento_posse_tamanho,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('locais')->update([
            'loc_documento_posse_path' => null,
            'loc_documento_posse_nome' => null,
            'loc_documento_posse_mime' => null,
            'loc_documento_posse_tamanho' => null,
        ]);
    }

    private function migrateLegacyMoradorDocumentos(): void
    {
        if (! Schema::hasColumn('moradores', 'mor_documento_pessoal_path')) {
            return;
        }

        $rows = DB::table('moradores')
            ->whereNotNull('mor_documento_pessoal_path')
            ->where('mor_documento_pessoal_path', '!=', '')
            ->get([
                'mor_id',
                'mor_documento_pessoal_path',
                'mor_documento_pessoal_nome',
                'mor_documento_pessoal_mime',
                'mor_documento_pessoal_tamanho',
            ]);

        foreach ($rows as $row) {
            DB::table('morador_documentos')->insert([
                'fk_morador_id' => $row->mor_id,
                'path' => $row->mor_documento_pessoal_path,
                'original_name' => $row->mor_documento_pessoal_nome,
                'mime' => $row->mor_documento_pessoal_mime,
                'size_bytes' => $row->mor_documento_pessoal_tamanho,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('moradores')->update([
            'mor_documento_pessoal_path' => null,
            'mor_documento_pessoal_nome' => null,
            'mor_documento_pessoal_mime' => null,
            'mor_documento_pessoal_tamanho' => null,
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('morador_documentos');
        Schema::dropIfExists('local_documentos');
    }
};
