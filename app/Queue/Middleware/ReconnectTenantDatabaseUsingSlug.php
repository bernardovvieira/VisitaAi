<?php

namespace App\Queue\Middleware;

use App\Models\RegistryTenant;
use App\Support\Tenancy\TenantConnection;
use Closure;
use RuntimeException;

class ReconnectTenantDatabaseUsingSlug
{
    public function __construct(
        public string $tenantSlug,
    ) {}

    public function handle(object $job, Closure $next): void
    {
        $tenant = RegistryTenant::query()
            ->where('slug', $this->tenantSlug)
            ->where('active', true)
            ->first();

        if ($tenant === null) {
            throw new RuntimeException("Tenant registry desconhecido ou inativo: {$this->tenantSlug}");
        }

        TenantConnection::configureMysqlFromRegistryTenant($tenant);
        TenantConnection::bindTenantToApplication($tenant, null);

        $next($job);
    }
}
