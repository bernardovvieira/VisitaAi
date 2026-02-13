<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coluna `login` = use_email para compatibilidade com Fortify/guard (WHERE login = ?).
     * Gerada sempre a partir de use_email: não aceita NULL e não duplica dados.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'login')) {
            DB::statement("ALTER TABLE users ADD COLUMN login VARCHAR(255) GENERATED ALWAYS AS (use_email) STORED NOT NULL");
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'login')) {
            DB::statement('ALTER TABLE users DROP COLUMN login');
        }
    }
};
