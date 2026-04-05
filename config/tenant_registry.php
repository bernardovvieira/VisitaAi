<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tenant registry (multi-instância dinâmica)
    |--------------------------------------------------------------------------
    |
    | Quando ativo, o host (subdomínio) escolhe o MySQL do município via tabela
    | registry_tenants na base REGISTRY_DB_*. Desativado por defeito: comportamento
    | atual (um .env + DB_*).
    |
    */
    'enabled' => filter_var(env('TENANT_REGISTRY_ENABLED', false), FILTER_VALIDATE_BOOLEAN)
        && filled(env('REGISTRY_DB_DATABASE')),

    /*
    | Domínios base (FQDN), sem protocolo. O primeiro label do host vira slug.
    | Ex.: visitaai.cloud → demo.visitaai.cloud usa slug "demo".
    | Vários: lista separada por vírgulas. Ordem não importa (casamento por sufixo).
    */
    'base_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('TENANT_REGISTRY_BASE_DOMAINS', ''))
    ))),

    /*
    | Em localhost / 127.0.0.1 força o slug (útil em dev sem DNS).
    */
    'dev_slug' => env('TENANT_DEV_SLUG'),

    /*
    | Após resolver tenant, força URL::root ao host atual (links absolutos).
    */
    'force_root_url' => filter_var(env('TENANT_REGISTRY_FORCE_ROOT_URL', true), FILTER_VALIDATE_BOOLEAN),

    /*
    | E-mails (use_email) que podem gerir o registry pela UI (/system/tenant-registry).
    | Lista separada por vírgulas. Vazio = rotas de administração não registadas.
    */
    'admin_emails' => array_values(array_filter(array_map(
        'strtolower',
        array_map('trim', explode(',', (string) env('REGISTRY_ADMIN_EMAILS', '')))
    ))),

    /*
    | Agendar tenants:migrate --force em todos os tenants (requer scheduler do sistema).
    */
    'schedule_migrate' => filter_var(env('TENANT_REGISTRY_SCHEDULE_MIGRATE', false), FILTER_VALIDATE_BOOLEAN),

    /*
    | Arranque automático (deploy): criar/atualizar 1 linha em registry_tenants.
    | Útil com mesma base para app + registry (REGISTRY_DB_DATABASE = DB_DATABASE).
    | artisan tenant-registry:bootstrap (e o entrypoint Docker chama após registry:migrate).
    */
    'bootstrap_enabled' => filter_var(env('TENANT_REGISTRY_BOOTSTRAP', false), FILTER_VALIDATE_BOOLEAN),

    'bootstrap_slug' => env('TENANT_REGISTRY_BOOTSTRAP_SLUG', 'demo'),

    'bootstrap_database' => env('TENANT_REGISTRY_BOOTSTRAP_DATABASE') ?: env('DB_DATABASE'),

    'bootstrap_environment' => env('TENANT_REGISTRY_BOOTSTRAP_ENV', 'production'),

    'bootstrap_display_name' => env('TENANT_REGISTRY_BOOTSTRAP_DISPLAY_NAME'),

    'bootstrap_brand' => env('TENANT_REGISTRY_BOOTSTRAP_BRAND'),

];
