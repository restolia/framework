<?php

declare(strict_types=1);

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Restolia\Support\Str;

class StrTest extends TestCase
{
    public function testGetDoesReturnExpected(): void
    {
        $this->assertSame('', (new Str())->get());
        $this->assertSame('foo', (new Str('foo'))->get());
    }

    public function testDoesReturnStrDotNotationFromArray(): void
    {
        $str = Str::dot(['foo' => true]);
        $this->assertSame('foo', (string)$str);

        $str = Str::dot(['foo' => ['bar' => true]]);
        $this->assertSame('foo.bar', (string)$str);
    }
}
