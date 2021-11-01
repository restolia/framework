<?php

declare(strict_types=1);

namespace Tests\Http;

use PHPUnit\Framework\TestCase;
use Restolia\Http\Response;
use Restolia\Kernel;
use Tests\Services\ServiceForRouteWithParameter;
use Tests\Services\ServiceWithHandlerWithoutSpecifyingClass;
use Tests\Services\ServiceWithConstructor;
use Tests\Services\ServiceWithoutConstructor;

class KernelTest extends TestCase
{
    public function testCanBootServiceWithoutConstructor(): void
    {
        ob_start();
        Kernel::boot(ServiceWithoutConstructor::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanBootServiceWithConstructor(): void
    {
        ob_start();
        Kernel::boot(ServiceWithConstructor::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanCallHandlerWithoutSpecifyingClass(): void
    {
        ob_start();
        Kernel::boot(ServiceWithHandlerWithoutSpecifyingClass::class);
        ob_end_flush();

        $this->assertSame('ok', ob_get_contents());
        ob_clean();
    }

    public function testCanCallHandlerWithParameters(): void
    {
        $_SERVER['REQUEST_URI'] = '/1';

        ob_start();
        Kernel::boot(ServiceForRouteWithParameter::class);
        ob_end_flush();

        $this->assertSame('1', ob_get_contents());
        ob_clean();
    }

    public function testDoesReturn404IfNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/not-found';

        ob_start();
        Kernel::boot(ServiceWithConstructor::class);
        ob_end_flush();

        $this->assertEmpty(ob_get_contents());
        ob_clean();

        /** @var Response $response */
        $response = Kernel::make(Response::class);
        $this->assertSame(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
