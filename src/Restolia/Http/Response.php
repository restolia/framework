<?php

namespace Restolia\Http;

use JsonException;

class Response extends \Symfony\Component\HttpFoundation\Response
{
    /**
     * @param array<mixed>|string $content
     * @return $this
     * @throws JsonException
     */
    public function json(array|string $content): Response
    {
        if (is_array($content)) {
            $content = json_encode($content, JSON_THROW_ON_ERROR);
        }

        $this->headers->set('Content-Type', 'application/json');
        $this->setContent($content);

        return $this;
    }
}
