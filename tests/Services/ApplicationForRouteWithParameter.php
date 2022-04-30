<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;

class ApplicationForRouteWithParameter extends Application
{
    public function routes(RouteCollector $router): void
    {
        $router->get('/{id}', 'handle');
    }

    public function handle(Response $response, string $id): void
    {
        $response->setContent($id);

        $response->send();
    }
}
