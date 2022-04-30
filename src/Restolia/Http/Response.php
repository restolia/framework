<?php

namespace Restolia\Http;

class Response extends \Symfony\Component\HttpFoundation\Response
{
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
