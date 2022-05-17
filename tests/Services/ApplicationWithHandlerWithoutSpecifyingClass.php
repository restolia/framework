<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;

class ApplicationWithHandlerWithoutSpecifyingClass extends Application
{
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
