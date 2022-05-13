<?php

namespace Restolia\Foundation;

use FastRoute\RouteCollector;

abstract class Application
{
    /**
     * Returns an array of providers that you would
     * like your application to have available.
     *
     * @return array<string>
     */
    public static function providers(): array
    {
        return [];
    }

    /**
     * Define your application's routes.
     *
     * @param  RouteCollector  $router
     */
    public function routes(RouteCollector $router): void
    {
    }
}