<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;
use Symfony\Component\Console\Command\Command;

class ApplicationForRouteWithParameter extends Application
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
        $router->get('/{id}', 'handle');
    }

    public function handle(Response $response, string $id): void
    {
        $response->setContent($id);

        $response->send();
    }
}
