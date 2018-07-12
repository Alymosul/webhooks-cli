<?php

namespace App\Services\HttpCalls;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GuzzleAdapter implements HttpCaller
{
    /**
     * Guzzle client instance.
     *
     * @var Client
     */
    private $guzzleClient;

    /**
     * GuzzleAdapter constructor.
     *
     * @param Client $guzzleClient
     */
    public function __construct(Client $guzzleClient)
    {
        $this->guzzleClient = $guzzleClient;
    }

    /**
     * Hits a given uri with a given message.
     *
     * @param string $uri
     * @param string $message
     *
     * @return bool
     */
    public function hit(string $uri, string $message)
    {
        try {
            $this->guzzleClient->post($uri, [$message]);
        } catch (GuzzleException $exception) {
            return false;
        }

        return true;
    }
}
