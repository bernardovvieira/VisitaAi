<?php

namespace App\Jobs;

use App\Queue\Middleware\ReconnectTenantDatabaseUsingSlug;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Classe base para jobs em fila que devem correr no MySQL do tenant.
 * Passa o slug registado em registry_tenants; o middleware reconecta antes do handle().
 */
abstract class TenantQueueJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly string $tenantSlug,
    ) {}

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [new ReconnectTenantDatabaseUsingSlug($this->tenantSlug)];
    }
}
