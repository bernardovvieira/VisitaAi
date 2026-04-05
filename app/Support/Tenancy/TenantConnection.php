<?php

namespace App\Support\Tenancy;

use App\Models\RegistryTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;

class TenantConnection
{
    /**
     * Aplica credenciais MySQL da ficha do tenant e reconecta a conexão "mysql".
     *
     * @throws \Throwable em falha de conexão (após reconnect)
     */
    public static function configureMysqlFromRegistryTenant(RegistryTenant $tenant): void
    {
        $mysqlConfig = config('database.connections.mysql', []);
        $mysqlConfig['database'] = $tenant->database;

        if (filled($tenant->db_host)) {
            $mysqlConfig['host'] = $tenant->db_host;
        }
        if (filled($tenant->db_username)) {
            $mysqlConfig['username'] = $tenant->db_username;
        }
        if ($tenant->db_password !== null && $tenant->db_password !== '') {
            $mysqlConfig['password'] = $tenant->db_password;
        }

        config(['database.connections.mysql' => $mysqlConfig]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    public static function bindTenantToApplication(RegistryTenant $tenant, ?Request $request = null): void
    {
        if ($request !== null) {
            $request->attributes->set('registry_tenant', $tenant);
        }
        app()->instance('registry.tenant', $tenant);

        if ($request !== null && config('tenant_registry.force_root_url')) {
            URL::forceRootUrl($request->getSchemeAndHttpHost());
        }
    }

    /**
     * Resolve tenant ativo por slug e aplica conexão (HTTP ou fila/CLI).
     */
    public static function applyBySlug(string $slug): ?RegistryTenant
    {
        $tenant = RegistryTenant::query()
            ->where('slug', $slug)
            ->where('active', true)
            ->first();

        if ($tenant === null) {
            return null;
        }
        self::configureMysqlFromRegistryTenant($tenant);

        return $tenant;
    }
}
