<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Restolia\Http\Response;
use Restolia\Kernel;
use Tests\Services\ApplicationForRouteWithParameter;
use Tests\Services\ApplicationWithHandlerWithoutSpecifyingClass;
use Tests\Services\ApplicationWithConstructor;
use Tests\Services\ApplicationWithoutConstructor;

class KernelTest extends TestCase
{
    public function testCanBootServiceWithoutConstructor(): void
    {
        ob_start();
        Kernel::boot(ApplicationWithoutConstructor::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanBootServiceWithConstructor(): void
    {
        ob_start();
        Kernel::boot(ApplicationWithConstructor::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanCallHandlerWithoutSpecifyingClass(): void
    {
        ob_start();
        Kernel::boot(ApplicationWithHandlerWithoutSpecifyingClass::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanCallHandlerWithParameters(): void
    {
        $_SERVER['REQUEST_URI'] = '/1';

        ob_start();
        Kernel::boot(ApplicationForRouteWithParameter::class);
        ob_end_flush();

        $this->assertSame('1', ob_get_contents());
        ob_clean();
    }

    public function testDoesReturn404IfNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/not-found';

        ob_start();
        Kernel::boot(ApplicationWithConstructor::class);
        ob_end_flush();

        $this->assertEmpty(ob_get_contents());
        ob_clean();

        /** @var Response $response */
        $response = Kernel::make(Response::class);
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
