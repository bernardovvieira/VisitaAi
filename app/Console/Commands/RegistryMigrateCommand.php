<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RegistryMigrateCommand extends Command
{
    protected $signature = 'registry:migrate {--force : Forçar execução em produção}';

    protected $description = 'Executa migrações apenas na base do registry (registry_tenants)';

    public function handle(): int
    {
        if (! filled(config('database.connections.registry.database'))) {
            $this->error('REGISTRY_DB_DATABASE não configurado.');

            return self::FAILURE;
        }

        $this->call('migrate', [
            '--database' => 'registry',
            '--path' => 'database/migrations/registry',
            '--force' => $this->option('force'),
        ]);

        return self::SUCCESS;
    }
}
