<?php

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Restolia\Http\Response;

class ResponseTest extends TestCase
{
    public function testJsonReturnsResponseClass(): void
    {
        $response = new Response();
        $this->assertSame($response, $response->json([]));
    }

    public function testJsonSetsHeader(): void
    {
        $response = new Response();
        $response->json([]);

        $this->assertTrue($response->headers->has('Content-Type'));
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testJsonWithString(): void
    {
        $expected = '[{"foo":"bar"}]';

        $response = new Response();
        $response->json($expected);

        $this->assertSame($expected, $response->getContent());
    }

    public function testJsonWithArray(): void
    {
        $expected = '[{"foo":"bar"}]';

        $response = new Response();
        $response->json([['foo' => 'bar']]);

        $this->assertSame($expected, $response->getContent());
    }
}
