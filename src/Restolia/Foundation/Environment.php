<?php

namespace Restolia\Foundation;

use Dotenv\Dotenv;

class Environment
{
    public function __construct(string ...$paths)
    {
        Dotenv::createImmutable($paths)->load();
    }

    /**
     * Determine if any of $environment values matches
     * the current environment based on the environment
     * variable "APP_ENV".
     *
     * If "APP_ENV" is not set then FALSE is returned.
     *
     * @param string ...$environment
     * @return bool
     */
    public function is(string ...$environment): bool
    {
        $current = $_ENV['APP_ENV'] ?? null;
        if ($current === null) {
            return false;
        }

        foreach ($environment as $value) {
            if ($current === $value) {
                return true;
            }
        }
        return false;
    }

    public function get(string $name, mixed $default = null): mixed
    {
        return $_ENV[$name] ?? $default;
    }
}