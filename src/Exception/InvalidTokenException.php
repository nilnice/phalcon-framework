<?php

namespace Nilnice\Phalcon\Exception;

use Throwable;

class InvalidTokenException extends \InvalidArgumentException
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * InvalidTokenException constructor.
     *
     * @param string          $message
     * @param int             $statusCode
     * @param int             $code
     * @param \Throwable|null $previous
     */
    public function __construct(
        string $message = '',
        int $statusCode = 0,
        int $code = 0,
        Throwable $previous = null
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
