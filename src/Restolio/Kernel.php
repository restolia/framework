<?php

namespace Restolia;

use DI\Container;
use DI\ContainerBuilder;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Restolia\Http\Response;
use Restolia\Service\Provider;
use Symfony\Component\HttpFoundation\Request;

class Kernel
{
    private Container $container;

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
        $this->container = (new ContainerBuilder())
            ->useAutowiring(true)
            ->build();
    }

    private function bindDependencies(): void
    {
        $this->container->set(Request::class, Request::createFromGlobals());
        $this->container->set(Response::class, new Response());
        $this->container->set(RouteCollector::class, new RouteCollector(new Std(), new MarkBased()));
    }

    private function bootService(): void
    {
        $this->bindProviders();

        $this->container->call([$this->service, '__construct']);
        $this->container->call([$this->service, 'boot']);
        $this->container->call([$this->service, 'routes']);
    }

    private function resolve(): void
    {
        /** @var Request $request */
        $request = $this->container->get(Request::class);

        /** @var RouteCollector $routes */
        $routes = $this->container->get(RouteCollector::class);

        [$state, $handler, $vars] = (new Dispatcher\GroupCountBased($routes->getData()))
            ->dispatch($request->getMethod(), $request->getPathInfo());

        /** @var Response $response */
        switch ($state) {
            case Dispatcher::NOT_FOUND:
                $response = $this->container->get(Response::class);
                $response->setStatusCode(Response::HTTP_NOT_FOUND);
                $response->send();
                break;
            case Dispatcher::METHOD_NOT_ALLOWED:
                $response = $this->container->get(Response::class);
                $response->setStatusCode(Response::HTTP_METHOD_NOT_ALLOWED);
                $response->send();
                break;
            case Dispatcher::FOUND:
                if (is_array($handler)) {
                    $this->container->call($handler, $vars);
                } else {
                    $this->container->call([$this->service, $handler], $vars);
                }
        }
    }

    private function bindProviders(): void
    {
        $providers = $this->container->call([$this->service, 'providers']);
        if (empty($providers)) {
            return;
        }

        foreach ($providers as $provider) {
            /** @var Provider $providerInstance */
            $this->container->call([$provider, 'register']);

            [$bindable, $instance] = $this->container->get($provider)->get();
            $this->container->set(
                $bindable,
                $instance
            );
        }
    }
}
