<?php

declare(strict_types=1);

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Http\Response;
use Restolia\Service\Service;

class ServiceWithHandlerWithoutSpecifyingClass extends Service
{
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
