<?php

declare(strict_types=1);

namespace Restolia\Support;

use Stringable;

class Str implements Stringable
{
    public function __construct(private string $value = '')
    {
    }

    /**
     * @param  array<string, mixed>  $arr
     * @param  array<string>         $keys
     * @return Str
     */
    public static function dot(array $arr, array $keys = []): self
    {
        foreach ($arr as $key => $value) {
            $keys[] = $key;

            if (is_array($value)) {
                return self::dot($value, $keys);
            }
        }

        return new self(implode('.', $keys));
    }

    public function get(): string
    {
        return $this->__toString();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
