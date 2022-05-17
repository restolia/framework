<?php

namespace Restolia;

use DI\Container;
use DI\ContainerBuilder;
use Exception;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use Restolia\Command\MakeHandlerCommand;
use Restolia\Command\VersionCommand;
use Restolia\Foundation\Provider;
use Restolia\Http\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;

class Kernel
{
    public const VERSION = '2.1.2';

    private string $app;

    private static Container $container;

    public function boot(string $app): Kernel
    {
        $this->app = $app;

        $this->setupContainer();
        $this->bindDependencies();

        $this->bootApplication();

        return $this;
    }

    private function setupContainer(): void
    {
        self::$container = (new ContainerBuilder())
            ->useAutowiring(true)
            ->build();
    }

    private function bindDependencies(): void
    {
        if ($this->isCli()) {
            self::set(Application::class, new Application());
            self::set(InputInterface::class, new ArgvInput());
            self::set(OutputInterface::class, new ConsoleOutput());
            return;
        }

        self::set(Request::class, Request::createFromGlobals());
        self::set(Response::class, new Response());
        self::set(RouteCollector::class, new RouteCollector(new Std(), new MarkBased()));
    }

    private function bootApplication(): void
    {
        $this->bindProviders();
        if (method_exists($this->app, '__construct')) {
            self::$container->call([$this->app, '__construct']);
        }

        if ($this->isCli()) {
            $this->registerCommands(self::$container->call([$this->app, 'commands']));
        } else {
            self::$container->call([$this->app, 'routes']);
        }
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

    /**
     * @param array<Command> $commands
     * @return void
     */
    private function registerCommands(array $commands): void
    {
        self::$container->call(
            [Application::class, 'addCommands'],
            [
                array_merge(
                    [
                        new VersionCommand(),
                        new MakeHandlerCommand(),
                    ],
                    $commands
                )
            ]
        );
    }

    public function isCli(): bool
    {
        return php_sapi_name() === 'cli';
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

    /**
     * Execute a command and return the exit code.
     *
     * @return int
     * @throws Exception
     */
    public function handle(): int
    {
        return (self::make(Application::class))
            ->run(
                self::make(InputInterface::class),
                self::make(OutputInterface::class),
            );
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
