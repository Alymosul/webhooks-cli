<?php

namespace App\Exceptions;

class CouldNotCreateWebhook extends BaseException
{
    /**
     * Default message used if no message was given to the constructor.
     *
     * @return string
     */
    public function defaultMessage()
    {
        return 'Invalid callback url was given.';
    }
}
