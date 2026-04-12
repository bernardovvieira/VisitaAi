<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConsoleInspireCommandTest extends TestCase
{
    #[Test]
    public function comando_inspire_executa_com_sucesso(): void
    {
        $this->artisan('inspire')
            ->assertExitCode(0);
    }
}
