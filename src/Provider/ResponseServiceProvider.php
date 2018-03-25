<?php

namespace Nilnice\Phalcon\Provider;

use Nilnice\Phalcon\Http\Response;
use Phalcon\DiInterface;

class ResponseServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'response';

    /**
     * Register response service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), Response::class);
    }
}
