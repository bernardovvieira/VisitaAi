<?php

namespace Tests\Unit;

use App\Support\Tenancy\HostSlugResolver;
use PHPUnit\Framework\TestCase;

class HostSlugResolverTest extends TestCase
{
    public function test_extracts_first_label_before_base_domain(): void
    {
        $r = new HostSlugResolver(['visitaai.cloud'], null);

        $this->assertSame('demo', $r->resolve('demo.visitaai.cloud'));
        $this->assertSame('ibirapuita', $r->resolve('ibirapuita.visitaai.cloud'));
    }

    public function test_longest_base_domain_wins(): void
    {
        $r = new HostSlugResolver(['cloud', 'visitaai.cloud'], null);

        $this->assertSame('demo', $r->resolve('demo.visitaai.cloud'));
    }

    public function test_apex_returns_null(): void
    {
        $r = new HostSlugResolver(['visitaai.cloud'], null);

        $this->assertNull($r->resolve('visitaai.cloud'));
    }

    public function test_localhost_uses_dev_slug(): void
    {
        $r = new HostSlugResolver([], 'demo');

        $this->assertSame('demo', $r->resolve('localhost'));
        $this->assertSame('demo', $r->resolve('127.0.0.1'));
    }

    public function test_unknown_domain_returns_null(): void
    {
        $r = new HostSlugResolver(['visitaai.cloud'], null);

        $this->assertNull($r->resolve('other.example.com'));
    }
}
