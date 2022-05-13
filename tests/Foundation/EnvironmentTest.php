<?php

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Restolia\Foundation\Environment;

class EnvironmentTest extends TestCase
{
    private Environment $environment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->environment = new Environment(dirname(__DIR__) . '/Foundation/data/');
    }

    public function testIs(): void
    {
        $this->assertTrue($this->environment->is('testing'));
        $this->assertFalse($this->environment->is('local'));
        $this->assertTrue($this->environment->is('testing', 'local'));
        $this->assertTrue($this->environment->is('local', 'testing'));
    }

    public function testGet(): void
    {
        $this->assertEquals('testing', $this->environment->get('APP_ENV'));
    }

    public function testGetWithDefault(): void
    {
        $this->assertEquals('foo', $this->environment->get('UNKNOWN', 'foo'));
        $this->assertTrue($this->environment->get('UNKNOWN', true));
        $this->assertFalse($this->environment->get('UNKNOWN', false));
        $this->assertNull($this->environment->get('UNKNOWN'));
    }
}