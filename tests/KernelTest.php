<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Restolia\Http\Response;
use Restolia\Kernel;
use Tests\Services\ApplicationForRouteWithParameter;
use Tests\Services\ApplicationWithConstructor;
use Tests\Services\ApplicationWithHandlerWithoutSpecifyingClass;
use Tests\Services\ApplicationWithoutConstructor;

/**
 * @runTestsInSeparateProcesses
 */
class KernelTest extends TestCase
{
    public function testCanBootServiceWithoutConstructor(): void
    {
        ob_start();
        (Kernel::boot(ApplicationWithoutConstructor::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanBootServiceWithConstructor(): void
    {
        ob_start();
        (Kernel::boot(ApplicationWithConstructor::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanCallHandlerWithoutSpecifyingClass(): void
    {
        ob_start();
        (Kernel::boot(ApplicationWithHandlerWithoutSpecifyingClass::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanCallHandlerWithParameters(): void
    {
        $_SERVER['REQUEST_URI'] = '/1';

        ob_start();
        (Kernel::boot(ApplicationForRouteWithParameter::class))->resolve();
        $this->assertEquals('1', ob_get_contents());
        ob_end_clean();
    }

    public function testDoesReturn404IfNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/not-found';

        ob_start();
        (Kernel::boot(ApplicationWithConstructor::class))->resolve();
        $this->assertEmpty(ob_get_contents());
        ob_end_clean();

        /** @var Response $response */
        $response = Kernel::make(Response::class);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }
}
