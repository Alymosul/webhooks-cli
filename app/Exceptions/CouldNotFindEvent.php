<?php

namespace App\Exceptions;

use Throwable;

class CouldNotFindEvent extends BaseException
{
    /**
     * The name of the event that could not be found.
     *
     * @var string
     */
    protected $eventName;

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
        $this->eventName = $eventName;
        parent::__construct($message, $code, $previous);
    }

    /**
     * Default message used if no message was given to the constructor.
     *
     * @return string
     */
    public function defaultMessage()
    {
        return "{$this->eventName} does not exist.";
    }
}
