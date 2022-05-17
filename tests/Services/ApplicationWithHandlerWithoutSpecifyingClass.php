<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;
use Symfony\Component\Console\Command\Command;

class ApplicationWithHandlerWithoutSpecifyingClass extends Application
{
    /**
     * @return array<Command>
     */
    public function commands(): array
    {
        return [];
    }

    public function routes(RouteCollector $router): void
    {
        // a route that only specifies the method to call
        $router->get('/', 'handle');
    }

    public function handle(Response $response): void
    {
        $response->setContent('ok');

        $response->send();
    }
}
