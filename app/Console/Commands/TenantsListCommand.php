<?php

namespace App\Console\Commands;

use App\Models\RegistryTenant;
use Illuminate\Console\Command;

class TenantsListCommand extends Command
{
    protected $signature = 'tenants:list';

    protected $description = 'Lista tenants registados na base registry';

    public function handle(): int
    {
        if (! config('tenant_registry.enabled')) {
            $this->warn('TENANT_REGISTRY_ENABLED está desligado (lista mesmo assim se registry estiver acessível).');
        }

        try {
            $rows = RegistryTenant::query()->orderBy('slug')->get();
        } catch (\Throwable $e) {
            $this->error('Não foi possível ler o registry: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($rows->isEmpty()) {
            $this->info('Nenhum tenant na tabela registry_tenants.');

            return self::SUCCESS;
        }

        $this->table(
            ['slug', 'environment', 'database', 'active', 'display_name'],
            $rows->map(fn (RegistryTenant $t) => [
                $t->slug,
                $t->environment,
                $t->database,
                $t->active ? 'yes' : 'no',
                $t->display_name ?? '—',
            ])->all()
        );

        return self::SUCCESS;
    }
}
