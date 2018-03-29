<?php

namespace Nilnice\Phalcon\Exception;

use ErrorException;
use Exception;

class ErrorHandler
{
    /**
     * Register exception and error handler.
     *
     * @return void
     */
    public static function register(): void
    {
        set_error_handler([__CLASS__, 'handleError']);
        set_exception_handler([__CLASS__, 'handleException']);
        register_shutdown_function([__CLASS__, 'handleShutdown']);
    }

    /**
     * Handle error.
     *
     * @param int    $type
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @return void
     */
    public static function handleError(
        int $type,
        string $message,
        string $file = '',
        int $line = 0
    ): void {
        $level = error_reporting()
            | E_RECOVERABLE_ERROR
            | E_USER_ERROR
            | E_DEPRECATED
            | E_USER_DEPRECATED;
        $e = new ErrorException($message, $type, $level, $file, $line);
        self::handleException($e);
    }

    /**
     * @param \Exception $e
     *
     * @return void
     */
    public static function handleException($e): void
    {
        if (! $e instanceof Exception) {
            $e = new ThrowableError($e);
        }

        self::getExceptionHandler()->render($e, PHP_SAPI === 'cli');
    }

    /**
     * @return void
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && self::isFatalError($error['type'])) {
            [
                'type'    => $type,
                'message' => $message,
                'file'    => $file,
                'line'    => $line,
            ]
                = $error;
            $exception = new ErrorException($type, $message, $file, $line);
            self::handleException($exception);
        }
    }

    /**
     * @return \Nilnice\Phalcon\Exception\Handler
     */
    public static function getExceptionHandler(): Handler
    {
        static $handler;

        if (! $handler) {
            $class = di('errorHandler');
            if (\is_string($class)
                && class_exists($class)
                && is_subclass_of($class, Handler::class)
            ) {
                $handler = new $class();
            } else {
                $handler = new Handler();
            }
        }

        return $handler;
    }

    /**
     * Confirm whether it is a fatal mistake.
     *
     * @param int $type
     *
     * @return bool
     */
    private static function isFatalError(int $type): bool
    {
        $error = [
            E_ERROR,
            E_CORE_ERROR,
            E_COMPILE_ERROR,
            E_PARSE,
        ];

        return \in_array($type, $error, true);
    }
}
