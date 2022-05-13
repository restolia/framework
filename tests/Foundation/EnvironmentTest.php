<?php

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Restolia\Foundation\Environment;

class EnvironmentTest extends TestCase
{
    public function testIs(): void
    {
        $environment = new Environment(dirname(__DIR__) . '/Foundation/data/');
        $this->assertTrue($environment->is('testing'));
        $this->assertFalse($environment->is('local'));
        $this->assertTrue($environment->is('testing', 'local'));
        $this->assertTrue($environment->is('local', 'testing'));
    }
}