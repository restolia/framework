<?php

namespace Restolia;

use DI\Container;
use DI\ContainerBuilder;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Restolia\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class Kernel
{
    private static Container $container;

    public static function boot(string $service): void
    {
        new self($service);
    }

    private function __construct(private string $service)
    {
        $this->setupContainer();
        $this->bindDependencies();

        $this->bootService();
        $this->resolve();
    }

    private function setupContainer(): void
    {
        self::$container = (new ContainerBuilder())
            ->useAutowiring(true)
            ->build();
    }

    private function bindDependencies(): void
    {
        self::$container->set(Request::class, Request::createFromGlobals());
        self::$container->set(Response::class, new Response());
        self::$container->set(RouteCollector::class, new RouteCollector(new Std(), new MarkBased()));
    }

    private function bootService(): void
    {
        $this->bindProviders();

        if (method_exists($this->service, '__construct')) {
            self::$container->call([$this->service, '__construct']);
        }
        self::$container->call([$this->service, 'boot']);
        self::$container->call([$this->service, 'routes']);
    }

    private function resolve(): void
    {
        /** @var Request $request */
        $request = self::$container->get(Request::class);

        /** @var RouteCollector $routes */
        $routes = self::$container->get(RouteCollector::class);

        [$state, $handler, $vars] = array_pad(
            (new Dispatcher\MarkBased($routes->getData()))
                ->dispatch($request->getMethod(), $request->getPathInfo()),
            3,
            null
        );

        switch ($state) {
            case Dispatcher::NOT_FOUND:
                $response = self::$container->get(Response::class);
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->send();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = self::$container->get(Response::class);
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                $response->send();
                break;
            case Dispatcher::FOUND:
                if (is_array($handler)) {
                    self::$container->call($handler, $vars ?? []);
                } else {
                    self::$container->call([$this->service, $handler], $vars ?? []);
                }
        }
    }

    private function bindProviders(): void
    {
        $providers = self::$container->call([$this->service, 'providers']);
        if (empty($providers)) {
            return;
        }

        foreach ($providers as $provider) {
            self::$container->call([$provider, 'register']);

            [$bindable, $instance] = self::$container->get($provider)->get();
            self::$container->set(
                $bindable,
                $instance
            );
        }
    }

    public static function make(string $implementation): mixed
    {
        return self::$container->get($implementation);
    }
}
