<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('users', 'use_ultimo_login_em')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('use_ultimo_login_em')->nullable()->after('use_senha')->index();
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('users', 'use_ultimo_login_em')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('use_ultimo_login_em');
        });
    }
};
