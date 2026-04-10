<?php

use Illuminate\Http\Request;

return [

    /*
    |--------------------------------------------------------------------------
    | Proxies confiáveis
    |--------------------------------------------------------------------------
    | Atrás de nginx, load balancer ou Cloudflare, defina para '*' ou liste
    | os IPs (ex.: '192.168.1.1' ou '10.0.0.0/8'). Assim $request->ip() e
    | $request->secure() usam os headers X-Forwarded-* corretamente.
    */

    'proxies' => env('APP_TRUSTED_PROXIES', '*'),

    /*
    |--------------------------------------------------------------------------
    | Headers de proxy
    |--------------------------------------------------------------------------
    */

    'headers' => Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO,
];
