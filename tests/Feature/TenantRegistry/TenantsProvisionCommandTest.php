<?php

namespace Tests\Feature\TenantRegistry;

use Tests\TestCase;

class TenantsProvisionCommandTest extends TestCase
{
    public function test_exits_when_provision_disabled(): void
    {
        config(['tenant_registry.provision_enabled' => false]);

        $this->artisan('tenants:provision', ['slug' => 'demo'])
            ->assertExitCode(1);
    }
}
