<?php

declare(strict_types=1);

namespace Restolia\Http;

use JsonException;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * @param  array<mixed, mixed>|string  $content
     * @throws JsonException
     */
    public function json(array|string $content): void
    {
        if (is_array($content)) {
            $content = json_encode($content, JSON_THROW_ON_ERROR);
        }

        $this->headers->set('Content-Type', 'application/json');
        $this->setContent($content);
    }
}
