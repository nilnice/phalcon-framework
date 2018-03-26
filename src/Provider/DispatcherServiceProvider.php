<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\DiInterface;
use Phalcon\Mvc\Dispatcher;

class DispatcherServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'dispatcher';

    /**
     * Register dispatcher service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () use ($di) {
            $route = config('route');
            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace($route->get('namespace'));
            $dispatcher->setDefaultController($route->get('controller'));
            $dispatcher->setActionName($route->get('action'));

            return $dispatcher;
        });
    }
}
