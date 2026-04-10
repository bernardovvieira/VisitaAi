<?php

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Illuminate\Http\Request;

class TrustProxies extends Middleware
{
    /**
     * Proxies confiáveis (vem de config/trustedproxy.php e APP_TRUSTED_PROXIES).
     * null = usa config; '*' = confia no IP que conecta (útil atrás de nginx/load balancer).
     */
    protected $proxies = null;

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO;
}
