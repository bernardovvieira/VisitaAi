<?php

namespace App\Http\Middleware;

use App\Models\RegistryTenant;
use App\Support\Tenancy\HostSlugResolver;
use App\Support\Tenancy\TenantConnection;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResolveTenantFromRegistry
{
    public function __construct(
        private readonly HostSlugResolver $hostSlugResolver,
    ) {}

    public function handle(Request $request, Closure $next): Response
    {
        if (! config('tenant_registry.enabled')) {
            return $next($request);
        }

        if (config('database.default') !== 'mysql') {
            return $next($request);
        }

        $slug = $this->hostSlugResolver->resolve($request->getHost());

        if ($slug === null) {
            return $next($request);
        }

        try {
            $tenant = RegistryTenant::query()
                ->where('slug', $slug)
                ->where('active', true)
                ->first();
        } catch (\Throwable $e) {
            Log::error('tenant_registry: falha ao ler registry', ['exception' => $e]);
            abort(503);
        }

        if ($tenant === null) {
            abort(404);
        }

        try {
            TenantConnection::configureMysqlFromRegistryTenant($tenant);
        } catch (\Throwable $e) {
            Log::error('tenant_registry: falha ao conectar MySQL do tenant', ['slug' => $slug, 'exception' => $e]);
            abort(503);
        }

        TenantConnection::bindTenantToApplication($tenant, $request);

        return $next($request);
    }
}
