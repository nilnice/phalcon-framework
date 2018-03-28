<?php

namespace Nilnice\Phalcon\Provider;

class CacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'cache';

    /**
     * Register cache service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), function () {
        });
    }
}
