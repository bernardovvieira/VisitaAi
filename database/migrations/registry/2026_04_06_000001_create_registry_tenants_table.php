<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('registry')->create('registry_tenants', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 64)->unique();
            $table->string('environment', 16)->default('production'); // sandbox | production
            $table->string('database')->comment('MySQL schema name for tenant application data');
            $table->string('db_host')->nullable();
            $table->string('db_username')->nullable();
            $table->text('db_password')->nullable();
            $table->string('display_name')->nullable();
            $table->string('brand', 128)->nullable();
            $table->json('settings')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('registry')->dropIfExists('registry_tenants');
    }
};
