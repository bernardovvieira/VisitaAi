<?php

$envBool = static function (mixed $value, bool $default): bool {
    if ($value === null || $value === '') {
        return $default;
    }
    if (is_bool($value)) {
        return $value;
    }

    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
};

return [

    /*
    |--------------------------------------------------------------------------
    | HTTP Strict Transport Security (HSTS)
    |--------------------------------------------------------------------------
    | Enviado só em respostas HTTPS. Em produção com HTTPS, o padrão é ativo.
    | includeSubDomains/preload: ative só se o domínio inteiro for HTTPS permanente.
    | SECURITY_HSTS=false trata-se corretamente (evitar (bool) "false" === true em PHP).
    */

    'hsts_enabled' => $envBool(env('SECURITY_HSTS'), env('APP_ENV') === 'production'),

    'hsts_max_age' => (int) env('SECURITY_HSTS_MAX_AGE', 31536000),

    'hsts_include_subdomains' => $envBool(env('SECURITY_HSTS_SUBDOMAINS'), false),

    'hsts_preload' => $envBool(env('SECURITY_HSTS_PRELOAD'), false),

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy (CSP)
    |--------------------------------------------------------------------------
    | Define se o header Content-Security-Policy deve ser enviado.
    | Em true, usa a política abaixo (compatível com login, Vite, Alpine, jQuery CDN).
    | Para desativar: SECURITY_CSP=false no .env
    */

    'csp_enabled' => $envBool(env('SECURITY_CSP'), true),

    /*
    |--------------------------------------------------------------------------
    | Política CSP (diretivas)
    |--------------------------------------------------------------------------
    | Ajuste se usar outros domínios (CDNs, analytics). Inclui 'unsafe-inline'
    | para scripts pois a tela de login usa script inline (máscara CPF) e Alpine.
    */

    'csp_directives' => [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://cdnjs.cloudflare.com https://cdn.jsdelivr.net https://unpkg.com https://viacep.com.br",
        "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://fonts.googleapis.com https://unpkg.com",
        "font-src 'self' data: https://fonts.bunny.net https://fonts.gstatic.com https://use.typekit.net",
        "img-src 'self' data: blob: https://*.tile.openstreetmap.org",
        "connect-src 'self' https://viacep.com.br https://nominatim.openstreetmap.org https://ipapi.co https://ipwho.is https://cdn.jsdelivr.net https://unpkg.com",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ],

    /*
    |--------------------------------------------------------------------------
    | Permissions-Policy
    |--------------------------------------------------------------------------
    | Habilita geolocalização no próprio site para suporte ao botão
    | "Minha Localização". Demais recursos continuam bloqueados por padrão.
    */

    'permissions_policy' => "geolocation=(self), microphone=(), camera=(), payment=(), usb=()",
];
