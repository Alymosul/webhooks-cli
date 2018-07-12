<?php

namespace App\Exceptions;

class NoRegisteredWebHooks extends BaseException
{
    /**
     * The name of the current event that has no webhooks.
     *
     * @var string
     */
    protected $eventName;

    public function __construct($eventName, $message = "", $code = 0, $previous = null)
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
        return "There are no webhooks registered for {$this->eventName}";
    }
}
