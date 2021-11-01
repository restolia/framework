<?php

declare(strict_types=1);

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Http\Response;
use Restolia\Service\Service;

class ServiceForRouteWithParameter extends Service
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
