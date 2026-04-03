<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Coluna login = use_email (Fortify/guard usam WHERE login = ?).
     * Só adiciona se a tabela foi criada antes dessa coluna estar na create_users.
     */
    public function up(): void
    {
        if (Schema::hasColumn('users', 'login')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('users', function ($table) {
                $table->string('login')->nullable();
            });

            return;
        }

        DB::statement('ALTER TABLE users ADD COLUMN login VARCHAR(255) GENERATED ALWAYS AS (use_email) STORED NOT NULL');
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'login')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            Schema::table('users', function ($table) {
                $table->dropColumn('login');
            });

            return;
        }

        DB::statement('ALTER TABLE users DROP COLUMN login');
    }
};
