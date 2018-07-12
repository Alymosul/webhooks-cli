<?php

namespace App\Exceptions;

class FailedResponseException extends BaseException
{
    /**
     * Default message used if no message was given to the constructor.
     *
     * @return string
     */
    public function defaultMessage()
    {
        return "The http call resulted in a failed response";
    }
}
