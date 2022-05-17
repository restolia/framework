<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Restolia\Http\Response;
use Restolia\Kernel;
use Symfony\Component\Console\Application;
use Tests\Services\ApplicationForRouteWithParameter;
use Tests\Services\ApplicationWithConstructor;
use Tests\Services\ApplicationWithHandlerWithoutSpecifyingClass;
use Tests\Services\ApplicationWithoutConstructor;

/**
 * @runTestsInSeparateProcesses
 */
class KernelTest extends TestCase
{
    private Kernel $kernel;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kernel = $this->createPartialMock(Kernel::class, ['isCli']);
        $this->kernel->method('isCli')->willReturn(false);
    }

    public function testCanBootServiceWithoutConstructor(): void
    {
        ob_start();
        ($this->kernel->boot(ApplicationWithoutConstructor::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanBootServiceWithConstructor(): void
    {
        ob_start();
        ($this->kernel->boot(ApplicationWithConstructor::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanCallHandlerWithoutSpecifyingClass(): void
    {
        ob_start();
        ($this->kernel->boot(ApplicationWithHandlerWithoutSpecifyingClass::class))->resolve();
        $this->assertEquals('ok', ob_get_contents());
        ob_end_clean();
    }

    public function testCanCallHandlerWithParameters(): void
    {
        $_SERVER['REQUEST_URI'] = '/1';

        ob_start();
        ($this->kernel->boot(ApplicationForRouteWithParameter::class))->resolve();
        $this->assertEquals('1', ob_get_contents());
        ob_end_clean();
    }

    public function testDoesReturn404IfNotFound(): void
    {
        $_SERVER['REQUEST_URI'] = '/not-found';

        ob_start();
        ($this->kernel->boot(ApplicationWithConstructor::class))->resolve();
        $this->assertEmpty(ob_get_contents());
        ob_end_clean();

        /** @var Response $response */
        $response = Kernel::make(Response::class);
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testConsoleHandle(): void
    {
        $application = $this->createPartialMock(Application::class, ['run']);
        $application->method('run')->willReturn(1);

        $this->kernel = (new Kernel())->boot(ApplicationWithConstructor::class);
        $this->kernel::set(Application::class, $application);

        $code = $this->kernel->handle();
        $this->assertEquals(1, $code);
    }
}
