<?php

namespace App\Console\Commands;

use App\Models\RegistryTenant;
use App\Support\Tenancy\TenantConnection;
use Illuminate\Console\Command;

class TenantsMigrateCommand extends Command
{
    protected $signature = 'tenants:migrate
                            {--tenant=* : Slug(s) específico(s); omite para todos os ativos}
                            {--force : Forçar em produção}
                            {--pretend : Apenas mostrar SQL}';

    protected $description = 'Corre migrate na conexão mysql de cada tenant (registry)';

    public function handle(): int
    {
        if (! config('tenant_registry.enabled')) {
            $this->warn('TENANT_REGISTRY_ENABLED está desligado.');
        }

        try {
            $query = RegistryTenant::query()->where('active', true)->orderBy('slug');
            $slugs = $this->option('tenant');
            if ($slugs !== []) {
                $query->whereIn('slug', $slugs);
            }
            $tenants = $query->get();
        } catch (\Throwable $e) {
            $this->error('Registry: '.$e->getMessage());

            return self::FAILURE;
        }

        if ($tenants->isEmpty()) {
            $this->warn('Nenhum tenant corresponde aos filtros.');

            return self::FAILURE;
        }

        foreach ($tenants as $tenant) {
            $this->line("→ <info>{$tenant->slug}</info> ({$tenant->database})");

            try {
                TenantConnection::configureMysqlFromRegistryTenant($tenant);
            } catch (\Throwable $e) {
                $this->error("  Falha ao conectar: {$e->getMessage()}");

                return self::FAILURE;
            }

            $opts = [];
            if ($this->option('force')) {
                $opts['--force'] = true;
            }
            if ($this->option('pretend')) {
                $opts['--pretend'] = true;
            }
            $code = $this->call('migrate', $opts);

            if ($code !== 0) {
                return $code;
            }
        }

        return self::SUCCESS;
    }
}
