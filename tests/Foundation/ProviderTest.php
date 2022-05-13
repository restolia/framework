<?php

namespace Tests\Foundation;

use PHPUnit\Framework\TestCase;
use Restolia\Foundation\Provider;

class ProviderTest extends TestCase
{
    public function testBindAndGet(): void
    {
        $provider = new class extends Provider {
            public function register(): void
            {
                $this->bind('Foo', new class{});
            }
        };
        $provider->register();

        [$bindable, $obj] = $provider->get();
        $this->assertEquals('Foo', $bindable);
        $this->assertIsObject($obj);
    }
}