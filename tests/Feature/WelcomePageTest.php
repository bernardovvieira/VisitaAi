<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WelcomePageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_publica_responde_200(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(__('Bem-vindo(a) ao'), false);
    }
}
