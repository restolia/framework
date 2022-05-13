<?php

namespace Restolia\Providers;

use Restolia\Foundation\Environment;
use Restolia\Foundation\Provider;

class EnvironmentProvider extends Provider
{
    /**
     * @var string[]
     */
    private array $paths;

    public function __construct(string ...$paths)
    {
        $this->paths = $paths;
    }

    public function register(): void
    {
        $this->bind(
            Environment::class,
            new Environment(...$this->paths)
        );
    }
}