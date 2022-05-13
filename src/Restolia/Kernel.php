<?php

namespace Restolia;

use DI\Container;
use DI\ContainerBuilder;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Restolia\Foundation\Provider;
use Restolia\Http\Response;
use Symfony\Component\HttpFoundation\Request;

class Kernel
{
    private static Container $container;

    public static function boot(string $app): Kernel
    {
        return new self($app);
    }

    private function __construct(private string $app)
    {
        $this->setupContainer();
        $this->bindDependencies();

        $this->bootApplication();
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

    private function bootApplication(): void
    {
        $this->bindProviders();

        if (method_exists($this->app, '__construct')) {
            self::$container->call([$this->app, '__construct']);
        }
        self::$container->call([$this->app, 'routes']);
    }

    private function bindProviders(): void
    {
        $providers = self::$container->call([$this->app, 'providers']);
        if (empty($providers)) {
            return;
        }

        foreach ($providers as $provider) {
            if ($provider instanceof Provider) {
                $provider->register();
                [$bindable, $instance] = $provider->get();
            } else {
                self::$container->call([$provider, 'register']);
                [$bindable, $instance] = self::$container->get($provider)->get();
            }


            self::$container->set(
                $bindable,
                $instance
            );
        }
    }

    public function resolve(): void
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
                    self::$container->call([$this->app, $handler], $vars ?? []);
                }
        }
    }

    public static function make(string $implementation): mixed
    {
        return self::$container->get($implementation);
    }

    public static function set(string $implementation, mixed $instance): void
    {
        self::$container->set($implementation, $instance);
    }
}
