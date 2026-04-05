<?php

namespace App\Support\Tenancy;

use App\Models\RegistryTenant;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class TenantProvisioner
{
    /**
     * Nome de schema MySQL padrão (prefixo + slug; hífens → underscore).
     */
    public static function defaultDatabaseName(string $slug): string
    {
        $slug = strtolower(trim($slug));
        if ($slug === '' || ! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            throw new InvalidArgumentException(__('Slug inválido para nome de base.'));
        }

        $suffix = str_replace('-', '_', $slug);
        $prefix = (string) config('tenant_registry.database_prefix', 'visita_');
        $name = $prefix.$suffix;

        if (strlen($name) > 64) {
            throw new InvalidArgumentException(__('Nome de base MySQL excede 64 caracteres.'));
        }

        if (! preg_match('/^[a-zA-Z0-9_]+$/', $name)) {
            throw new InvalidArgumentException(__('Nome de base derivado do slug é inválido.'));
        }

        $registryDb = (string) config('database.connections.registry.database');
        if ($registryDb !== '' && $name === $registryDb) {
            throw new InvalidArgumentException(
                __('A base do registry não pode ser usada como base de tenant. Ajuste TENANT_DATABASE_PREFIX ou o slug.')
            );
        }

        return $name;
    }

    /**
     * @param  array<string, mixed>  $attributes  slug, environment, database?, display_name?, brand?, settings?, active?, db_host?, db_username?, db_password?
     */
    public function provisionNewTenant(array $attributes, bool $runMigrate = true): RegistryTenant
    {
        if (! config('tenant_registry.provision_enabled')) {
            throw new InvalidArgumentException(__('Provisionamento MySQL está desligado (TENANT_PROVISION_ENABLED).'));
        }

        $slug = (string) ($attributes['slug'] ?? '');
        $environment = (string) ($attributes['environment'] ?? 'production');
        $database = isset($attributes['database']) && $attributes['database'] !== ''
            ? (string) $attributes['database']
            : self::defaultDatabaseName($slug);

        $this->assertDatabaseNameSafe($database);

        if (RegistryTenant::query()->where('slug', $slug)->exists()) {
            throw new InvalidArgumentException(__('Já existe um tenant com este slug.'));
        }

        $registryDb = (string) config('database.connections.registry.database');
        if ($registryDb !== '' && $database === $registryDb) {
            throw new InvalidArgumentException(__('Base inválida: coincide com a base do registry.'));
        }

        $row = $this->toRegistryAttributes($attributes, $slug, $environment, $database);

        try {
            $this->createTenantSchemaAndMigrate($database, $runMigrate);
        } catch (\Throwable $e) {
            try {
                $this->dropDatabase($database);
            } catch (\Throwable) {
            }
            throw $e;
        }

        return RegistryTenant::query()->create($row);
    }

    /**
     * Garante schema MySQL + migrate no arranque (bootstrap), quando o tenant usa base dedicada.
     * Com uma única base (tenant = registry), não faz nada — o entrypoint já migrou.
     */
    public function ensureTenantMysqlSchemaForBootstrap(string $database, bool $runMigrate = true): void
    {
        if (! config('tenant_registry.provision_enabled')) {
            return;
        }

        $this->assertDatabaseNameSafe($database);
        $registryDb = (string) config('database.connections.registry.database');
        if ($registryDb !== '' && $database === $registryDb) {
            return;
        }

        $this->createTenantSchemaAndMigrate($database, $runMigrate);
    }

    private function createTenantSchemaAndMigrate(string $database, bool $runMigrate): void
    {
        $this->assertDatabaseNameSafe($database);
        $this->createDatabaseAndGrants($database);
        if (! $runMigrate) {
            return;
        }

        $ghost = new RegistryTenant(['database' => $database]);
        TenantConnection::configureMysqlFromRegistryTenant($ghost);
        $code = Artisan::call('migrate', ['--force' => true]);
        if ($code !== 0) {
            throw new InvalidArgumentException(__('Falha ao executar migrate na base do tenant.'));
        }
    }

    private function assertDatabaseNameSafe(string $database): void
    {
        if ($database === '' || strlen($database) > 64 || ! preg_match('/^[a-zA-Z0-9_]+$/', $database)) {
            throw new InvalidArgumentException(__('Nome de base MySQL inválido.'));
        }
    }

    private function createDatabaseAndGrants(string $database): void
    {
        $escaped = str_replace('`', '``', $database);
        DB::connection('tenant_provision')->statement(
            "CREATE DATABASE IF NOT EXISTS `{$escaped}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
        );

        $grantUser = config('tenant_registry.provision_grant_app_username');
        if (! filled($grantUser)) {
            return;
        }

        $grantHost = (string) config('tenant_registry.provision_grant_app_host', '%');
        $u = str_replace(['\\', "'"], ['\\\\', "''"], (string) $grantUser);
        $h = str_replace(['\\', "'"], ['\\\\', "''"], $grantHost);

        DB::connection('tenant_provision')->statement(
            "GRANT ALL PRIVILEGES ON `{$escaped}`.* TO '{$u}'@'{$h}'"
        );
        DB::connection('tenant_provision')->statement('FLUSH PRIVILEGES');
    }

    private function dropDatabase(string $database): void
    {
        $escaped = str_replace('`', '``', $database);
        DB::connection('tenant_provision')->statement("DROP DATABASE IF EXISTS `{$escaped}`");
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    private function toRegistryAttributes(array $attributes, string $slug, string $environment, string $database): array
    {
        $row = [
            'slug' => $slug,
            'environment' => $environment,
            'database' => $database,
            'active' => (bool) ($attributes['active'] ?? true),
        ];

        foreach (['db_host', 'db_username', 'display_name', 'brand', 'settings'] as $key) {
            if (array_key_exists($key, $attributes) && $attributes[$key] !== null && $attributes[$key] !== '') {
                $row[$key] = $attributes[$key];
            }
        }

        if (! empty($attributes['db_password'])) {
            $row['db_password'] = $attributes['db_password'];
        }

        return $row;
    }
}
