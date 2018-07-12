<?php

namespace App\Services\Jobs\Reactors;

class HttpCallsReactor
{
    /**
     * Identify the right strategy to react for an http call.
     *
     * @param bool $successfulResponse
     *
     * @return ReactorInterface
     */
    public static function getStrategy(bool $successfulResponse)
    {
        if ($successfulResponse) {
            return new SuccessReactor;
        }

        return new FailReactor;
    }
}
