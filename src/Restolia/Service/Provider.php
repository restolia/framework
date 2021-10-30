<?php

namespace Restolia\Service;

abstract class Provider
{
    private string $bindable;

    private object $instance;

    abstract public function register(): void;

    protected function bind(string $bindable, object $instance): void
    {
        $this->bindable = $bindable;
        $this->instance = $instance;
    }

    /**
     * @return array<int, object|string>
     */
    public function get(): array
    {
        return [$this->bindable, $this->instance];
    }
}
