<?php

namespace Nilnice\Phalcon\Provider;

use Nilnice\Phalcon\Http\Request;
use Phalcon\DiInterface;

class RequestServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'request';

    /**
     * Register request service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), Request::class);
    }
}
