<?php

namespace Tests\Unit;

use App\Support\Tenancy\TenantProvisioner;
use InvalidArgumentException;
use Tests\TestCase;

class TenantProvisionerTest extends TestCase
{
    public function test_default_database_name_uses_prefix_and_underscores(): void
    {
        config(['tenant_registry.database_prefix' => 'visita_']);

        $this->assertSame('visita_demo', TenantProvisioner::defaultDatabaseName('demo'));
        $this->assertSame('visita_demo_city', TenantProvisioner::defaultDatabaseName('demo-city'));
    }

    public function test_default_database_name_rejects_invalid_slug(): void
    {
        $this->expectException(InvalidArgumentException::class);

        TenantProvisioner::defaultDatabaseName('demo_');
    }

    public function test_default_database_name_rejects_colliding_with_registry(): void
    {
        config([
            'tenant_registry.database_prefix' => 'visita_',
            'database.connections.registry.database' => 'visita_demo',
        ]);

        $this->expectException(InvalidArgumentException::class);

        TenantProvisioner::defaultDatabaseName('demo');
    }
}
