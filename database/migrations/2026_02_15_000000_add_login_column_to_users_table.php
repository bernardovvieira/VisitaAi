<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coluna login = use_email para bancos que rodaram a migration original antes dela incluir login.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'login')) {
            DB::statement('ALTER TABLE users ADD COLUMN login VARCHAR(255) GENERATED ALWAYS AS (use_email) STORED NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'login')) {
            DB::statement('ALTER TABLE users DROP COLUMN login');
        }
    }
};
