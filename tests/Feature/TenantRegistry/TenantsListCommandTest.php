<?php

namespace Tests\Feature\TenantRegistry;

use App\Models\RegistryTenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantsListCommandTest extends TestCase
{
    use RefreshDatabase;

    private string $registrySqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrySqlitePath = database_path('testing_registry_list.sqlite');
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

    public function test_tenants_list_shows_slugs(): void
    {
        RegistryTenant::query()->create([
            'slug' => 'demo',
            'environment' => 'sandbox',
            'database' => 'visita_demo',
            'active' => true,
        ]);

        $this->artisan('tenants:list')
            ->expectsOutputToContain('demo')
            ->assertExitCode(0);
    }
}
