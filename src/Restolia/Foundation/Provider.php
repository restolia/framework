<?php

namespace Restolia\Foundation;

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

    public function get(): array
    {
        return [$this->bindable, $this->instance];
    }
}
