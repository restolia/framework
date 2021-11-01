<?php

declare(strict_types=1);

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Restolia\Environment\Config;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::initialize();
    }

    public function testCanPutAndGet(): void
    {
        Config::put('foo', 'bar');
        $this->assertSame('bar', Config::get('foo'));

        Config::put('foo', 1);
        $this->assertSame(1, Config::get('foo'));

        Config::put('foo', 1.0);
        $this->assertSame(1.0, Config::get('foo'));

        Config::put('foo', true);
        $this->assertTrue(Config::get('foo'));

        Config::put('foo', false);
        $this->assertFalse(Config::get('foo'));
    }

    public function testCanGetWithDefaultValue(): void
    {
        $this->assertNull(Config::get('foo'));

        $this->assertSame('default', Config::get('foo', 'default'));
    }

    public function testCanPutAndGetWithDotNotation(): void
    {
        Config::put(['bar' => []], true);
        $this->assertTrue(Config::get('bar'));

        Config::put(['foo' => ['bar' => true]], true);
        $this->assertTrue(Config::get('foo.bar'));
    }

    public function testCanGetValueFromEnv(): void
    {
        putenv('TEST_FOO=bar');
        $this->assertSame('bar', Config::env('TEST_FOO'));
    }

    public function testCanGetDefaultValueFromEnv(): void
    {
        $this->assertNull(Config::env('TEST_NULL'));
        $this->assertSame('foo', Config::env('TEST_NULL', 'foo'));
    }
}
