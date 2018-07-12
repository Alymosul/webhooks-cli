<?php

namespace App\Exceptions;

use Throwable;

abstract class BaseException extends \Exception
{
    /**
     * Exception constructor.
     *
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        $message = empty(trim($message))? $this->defaultMessage() : $message;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Default message used if no message was given to the constructor.
     *
     * @return string
     */
    abstract public function defaultMessage();
}
