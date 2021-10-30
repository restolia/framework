<?php

namespace Restolia\Service;

use FastRoute\RouteCollector;

abstract class Service
{
    /**
     * Returns an array of providers that you would
     * like your service to have available.
     *
     * @return array<string>
     */
    public static function providers(): array
    {
        return [];
    }

    /**
     * The boot method is called before handle()
     * and should be used to perform any setup tasks.
     *
     * @return void
     */
    public function boot(): void
    {
    }

    /**
     * Define your services routes.
     *
     * @param  RouteCollector  $router
     */
    public function routes(RouteCollector $router): void
    {
    }
}
