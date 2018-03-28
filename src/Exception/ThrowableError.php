<?php

namespace Nilnice\Phalcon\Exception;

class ThrowableError extends \ErrorException
{
    /**
     * ThrowableError constructor.
     *
     * @param \Throwable $e
     */
    public function __construct(\Throwable $e)
    {
        $message = $e->getMessage();

        if ($e instanceof \ParseError) {
            $message = 'Parse error: ' . $message;
            $severity = E_PARSE;
        } elseif ($e instanceof \TypeError) {
            $message = 'Type error: ' . $message;
            $severity = E_RECOVERABLE_ERROR;
        } else {
            $message = 'Fatal error: ' . $message;
            $severity = E_ERROR;
        }

        parent::__construct(
            $message,
            $e->getCode(),
            $severity,
            $e->getFile(),
            $e->getLine(),
            $e->getPrevious()
        );
    }
}
