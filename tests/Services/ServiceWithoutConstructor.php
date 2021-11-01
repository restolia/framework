<?php

declare(strict_types=1);

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Http\Response;
use Restolia\Service\Service;

class ServiceWithoutConstructor extends Service
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
