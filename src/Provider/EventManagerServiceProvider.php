<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\DiInterface;
use Phalcon\Events\Manager;

class EventManagerServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'eventsManager';

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
        $di->setShared($this->getName(), function () {
            $manager = new Manager();
            $manager->enablePriorities(true);

            return $manager;
        });
    }
}
