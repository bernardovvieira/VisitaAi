<?php

namespace App\Console\Commands;

use App\Models\RegistryTenant;
use Illuminate\Console\Command;

class TenantRegistryBootstrapCommand extends Command
{
    protected $signature = 'tenant-registry:bootstrap
                            {--fresh-registry : Corre registry:migrate antes (idempotente)}';

    protected $description = 'Cria/atualiza tenant inicial (TENANT_REGISTRY_BOOTSTRAP_*). Requer registry ativo.';

    public function handle(): int
    {
        if (! config('tenant_registry.enabled')) {
            return self::SUCCESS;
        }

        if ($this->option('fresh-registry')) {
            $this->call('registry:migrate', ['--force' => true]);
        }

        if (! config('tenant_registry.bootstrap_enabled')) {
            return self::SUCCESS;
        }

        $slug = (string) config('tenant_registry.bootstrap_slug');
        $database = (string) config('tenant_registry.bootstrap_database');

        if ($slug === '' || $database === '') {
            $this->error('Defina TENANT_REGISTRY_BOOTSTRAP_SLUG e base (ou DB_DATABASE).');

            return self::FAILURE;
        }

        $attrs = [
            'environment' => (string) config('tenant_registry.bootstrap_environment'),
            'database' => $database,
            'active' => true,
        ];
        $display = config('tenant_registry.bootstrap_display_name');
        $brand = config('tenant_registry.bootstrap_brand');
        if (filled($display)) {
            $attrs['display_name'] = $display;
        }
        if (filled($brand)) {
            $attrs['brand'] = $brand;
        }

        RegistryTenant::query()->updateOrCreate(
            ['slug' => $slug],
            $attrs
        );

        $this->info("Registry: tenant «{$slug}» → MySQL database «{$database}».");

        return self::SUCCESS;
    }
}
