<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function resposta_web_inclui_headers_basicos_de_seguranca(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-DNS-Prefetch-Control', 'off');
        $response->assertHeader('X-Permitted-Cross-Domain-Policies', 'none');
        $response->assertHeader('Permissions-Policy', 'geolocation=(self), microphone=(), camera=(), payment=(), usb=()');
        $response->assertHeader('Content-Security-Policy');
    }

    #[Test]
    public function csp_pode_ser_desabilitada_por_configuracao(): void
    {
        config()->set('security-headers.csp_enabled', false);

        $response = $this->get('/');

        $response->assertHeaderMissing('Content-Security-Policy');
    }

    #[Test]
    public function hsts_e_enviado_em_https_quando_habilitado(): void
    {
        config()->set('security-headers.hsts_enabled', true);
        config()->set('security-headers.hsts_max_age', 31536000);
        config()->set('security-headers.hsts_include_subdomains', true);

        $response = $this->get('https://localhost/');

        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
