<?php

namespace App\Exceptions;

use Throwable;

class CouldNotCreateWebhook extends \Exception
{
    /**
     * CouldNotCreateWebhook constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "Invalid callback url was given.", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
