<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistryTenant extends Model
{
    protected $connection = 'registry';

    protected $table = 'registry_tenants';

    protected $fillable = [
        'slug',
        'environment',
        'database',
        'db_host',
        'db_username',
        'db_password',
        'display_name',
        'brand',
        'settings',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'db_password' => 'encrypted',
            'settings' => 'array',
        ];
    }

    public function isSandbox(): bool
    {
        return $this->environment === 'sandbox';
    }
}
