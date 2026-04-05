<?php

namespace Tests\Feature\TenantRegistry;

use App\Models\RegistryTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantRegistryBootstrapCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $registrySqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrySqlitePath = database_path('testing_registry_bootstrap.sqlite');
        if (is_file($this->registrySqlitePath)) {
            unlink($this->registrySqlitePath);
        }
        touch($this->registrySqlitePath);

        config([
            'database.connections.registry' => [
                'driver' => 'sqlite',
                'database' => $this->registrySqlitePath,
                'prefix' => '',
            ],
            'tenant_registry.enabled' => true,
            'tenant_registry.bootstrap_enabled' => true,
            'tenant_registry.bootstrap_slug' => 'demo',
            'tenant_registry.bootstrap_database' => 'visita_demo',
            'tenant_registry.bootstrap_environment' => 'sandbox',
        ]);

        $this->artisan('migrate:fresh', [
            '--database' => 'registry',
            '--path' => 'database/migrations/registry',
            '--force' => true,
        ])->assertExitCode(0);
    }

    protected function tearDown(): void
    {
        if (isset($this->registrySqlitePath) && is_file($this->registrySqlitePath)) {
            @unlink($this->registrySqlitePath);
        }
        parent::tearDown();
    }

    public function test_bootstrap_creates_tenant(): void
    {
        $this->artisan('tenant-registry:bootstrap')->assertExitCode(0);

        $row = RegistryTenant::query()->where('slug', 'demo')->first();
        $this->assertNotNull($row);
        $this->assertSame('visita_demo', $row->database);
        $this->assertSame('sandbox', $row->environment);
        $this->assertTrue($row->active);
    }

    public function test_bootstrap_idempotent(): void
    {
        $this->artisan('tenant-registry:bootstrap')->assertExitCode(0);
        $this->artisan('tenant-registry:bootstrap')->assertExitCode(0);

        $this->assertSame(1, RegistryTenant::query()->count());
    }

    public function test_bootstrap_skips_when_registry_disabled(): void
    {
        config(['tenant_registry.enabled' => false]);

        $this->artisan('tenant-registry:bootstrap')->assertExitCode(0);

        $this->assertSame(0, RegistryTenant::query()->count());
    }

    public function test_bootstrap_skips_when_bootstrap_disabled(): void
    {
        config(['tenant_registry.bootstrap_enabled' => false]);

        $this->artisan('tenant-registry:bootstrap')->assertExitCode(0);

        $this->assertSame(0, RegistryTenant::query()->count());
    }
}
