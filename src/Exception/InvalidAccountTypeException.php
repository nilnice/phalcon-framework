<?php

namespace Nilnice\Phalcon\Exception;

use Throwable;

class InvalidAccountTypeException extends \InvalidArgumentException
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * InvalidAccountTypeException constructor.
     *
     * @param string          $message
     * @param int             $statusCode
     * @param \Throwable|null $previous
     * @param int             $code
     */
    public function __construct(
        string $message = '',
        int $statusCode = 0,
        Throwable $previous = null,
        int $code = 0
    ) {
        $this->statusCode = $statusCode;

        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
