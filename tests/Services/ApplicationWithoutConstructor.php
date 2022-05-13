<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;

class ApplicationWithoutConstructor extends Application
{
    public function routes(RouteCollector $router): void
    {
        $router->get('/', [self::class, 'handle']);
    }

    public function handle(Response $response): void
    {
        $response->setContent('ok');

        $response->send();
    }
}