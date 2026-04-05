<?php

namespace App\Console\Commands;

use App\Support\Tenancy\TenantProvisioner;
use Illuminate\Console\Command;

class TenantsProvisionCommand extends Command
{
    protected $signature = 'tenants:provision
                            {slug : Subdomínio (ex.: ibirapuita)}
                            {--database= : Nome do schema MySQL; omite para prefixo+slug}
                            {--environment=production : sandbox ou production}
                            {--display-name= : Nome exibido na app}
                            {--brand= : Marca curta}
                            {--no-migrate : Só criar base e registo (sem migrate)}';

    protected $description = 'Cria schema MySQL, corre migrate e regista tenant (TENANT_PROVISION_ENABLED=true)';

    public function handle(TenantProvisioner $provisioner): int
    {
        if (! config('tenant_registry.provision_enabled')) {
            $this->error('Ligue TENANT_PROVISION_ENABLED=true e use credenciais com CREATE DATABASE na conexão tenant_provision.');

            return self::FAILURE;
        }

        $environment = strtolower(trim((string) $this->option('environment')));
        if (! in_array($environment, ['sandbox', 'production'], true)) {
            $this->error('--environment deve ser sandbox ou production.');

            return self::FAILURE;
        }

        $slug = strtolower(trim((string) $this->argument('slug')));
        $databaseOption = $this->option('database');
        $database = $databaseOption !== null && $databaseOption !== ''
            ? (string) $databaseOption
            : null;

        $payload = [
            'slug' => $slug,
            'environment' => $environment,
            'active' => true,
        ];
        if ($database !== null) {
            $payload['database'] = $database;
        }
        $display = $this->option('display-name');
        if ($display !== null && $display !== '') {
            $payload['display_name'] = $display;
        }
        $brand = $this->option('brand');
        if ($brand !== null && $brand !== '') {
            $payload['brand'] = $brand;
        }

        try {
            $tenant = $provisioner->provisionNewTenant($payload, ! $this->option('no-migrate'));
        } catch (\Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $this->info("Tenant «{$tenant->slug}» → «{$tenant->database}» pronto.");

        return self::SUCCESS;
    }
}
