<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy (CSP)
    |--------------------------------------------------------------------------
    | Define se o header Content-Security-Policy deve ser enviado.
    | Em true, usa a política abaixo (compatível com login, Vite, Alpine, jQuery CDN).
    | Para desativar: SECURITY_CSP=false no .env
    */

    'csp_enabled' => env('SECURITY_CSP', true),

    /*
    |--------------------------------------------------------------------------
    | Política CSP (diretivas)
    |--------------------------------------------------------------------------
    | Ajuste se usar outros domínios (CDNs, analytics). Inclui 'unsafe-inline'
    | para scripts pois a tela de login usa script inline (máscara CPF) e Alpine.
    */

    'csp_directives' => [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://unpkg.com",
        "style-src 'self' 'unsafe-inline' https://fonts.bunny.net https://unpkg.com",
        "font-src 'self' https://fonts.bunny.net",
        "img-src 'self' data: blob: https://*.tile.openstreetmap.org",
        "connect-src 'self'",
        "base-uri 'self'",
        "form-action 'self'",
        "frame-ancestors 'self'",
    ],
];
