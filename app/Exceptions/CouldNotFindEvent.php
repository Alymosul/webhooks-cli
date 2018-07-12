<?php

namespace App\Exceptions;

use Throwable;

class CouldNotFindEvent extends \Exception
{
    /**
     * CouldNotFindEvent constructor.
     *
     * @param string $eventName
     * @param string $message
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct($eventName, $message = "", $code = 0, Throwable $previous = null)
    {
        $message = "{$eventName} does not exist.";
        parent::__construct($message, $code, $previous);
    }
}
