<?php

namespace Tests\Feature\TenantRegistry;

use App\Models\RegistryTenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistryAdminTest extends TestCase
{
    use RefreshDatabase;

    private string $registrySqlitePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->registrySqlitePath = database_path('testing_registry_admin.sqlite');
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

    public function test_registry_index_requires_auth(): void
    {
        $this->get(route('registry.admin.index'))->assertRedirect();
    }

    public function test_registry_index_forbidden_for_non_admin_email(): void
    {
        $user = User::factory()->create([
            'use_email' => 'other@test.com',
            'use_aprovado' => true,
        ]);

        $this->actingAs($user)->get(route('registry.admin.index'))->assertForbidden();
    }

    public function test_registry_index_ok_for_admin_email(): void
    {
        RegistryTenant::query()->create([
            'slug' => 'x',
            'environment' => 'production',
            'database' => 'db_x',
            'active' => true,
        ]);

        $user = User::factory()->create([
            'use_email' => 'registry-admin@test.com',
            'use_aprovado' => true,
        ]);

        $this->actingAs($user)->get(route('registry.admin.index'))
            ->assertOk()
            ->assertSee('x');
    }
}
