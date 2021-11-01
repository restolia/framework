<?php

declare(strict_types=1);

namespace Restolia\Environment;

use Restolia\Support\Str;

class Config
{
    /**
     * @var array<string, mixed>
     */
    private static array $configuration;

    public static function initialize(): void
    {
        self::$configuration = [];
    }

    /**
     * @param  string|array<string, mixed>  $name
     * @param  string|int|float|bool|null   $value
     */
    public static function put(string|array $name, string|int|float|bool|null $value): void
    {
        self::$configuration[is_array($name) ? Str::dot($name)->get() : $name] = $value;
    }

    public static function get(string $name, string $default = null): string|int|float|bool|null
    {
        return self::$configuration[$name] ?? $default;
    }

    public static function env(string $name, string|int|float|bool|null $default = null): string|int|float|bool|null
    {
        return getenv($name) ?: $default;
    }
}
