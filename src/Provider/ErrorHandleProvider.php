<?php

namespace Nilnice\Phalcon\Provider;

class ErrorHandleProvider extends AbstractServiceProvider
{
    protected $name = 'errorHandler';

    /**
     * Register error handle provider.
     *
     * @param mixed|null $parameter
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), function () {
            $handler = config('error_handler');

            return new $handler();
        });
    }
}
