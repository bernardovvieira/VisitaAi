<?php

namespace App\Console\Commands;

use App\Models\RegistryTenant;
use App\Support\Tenancy\TenantConnection;
use Illuminate\Console\Command;

class TenantsSeedCommand extends Command
{
    protected $signature = 'tenants:seed
                            {--tenant=* : Slug(s); omite para todos os ativos}
                            {--class= : Classe seeder}
                            {--force : Forçar em produção}';

    protected $description = 'Corre db:seed na conexão mysql de cada tenant';

    public function handle(): int
    {
        $class = $this->option('class');
        if (! filled($class)) {
            $this->error('Indique --class=Database\\Seeders\\...');

            return self::FAILURE;
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
            $this->line("→ <info>{$tenant->slug}</info>");

            try {
                TenantConnection::configureMysqlFromRegistryTenant($tenant);
            } catch (\Throwable $e) {
                $this->error("  Conexão: {$e->getMessage()}");

                return self::FAILURE;
            }

            $code = $this->call('db:seed', [
                '--class' => $class,
                '--force' => $this->option('force'),
            ]);

            if ($code !== 0) {
                return $code;
            }
        }

        return self::SUCCESS;
    }
}
