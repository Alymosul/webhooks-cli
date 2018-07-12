<?php

namespace App\Services\HttpCalls;

interface HttpCaller
{
    /**
     * Hits a given uri with a given message.
     *
     * @param string $uri
     * @param string $message
     *
     * @return bool
     */
    public function hit(string $uri, string $message);
}
