<?php

namespace Tests\Services;

use FastRoute\RouteCollector;
use Restolia\Foundation\Application;
use Restolia\Http\Response;
use Symfony\Component\Console\Command\Command;

class ApplicationWithConstructor extends Application
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
        $router->get('/', [self::class, 'handle']);
    }

    public function handle(Response $response): void
    {
        $response->setContent('ok');

        $response->send();
    }
}
