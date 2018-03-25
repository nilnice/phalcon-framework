<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Mvc\Model\Manager;

class ModelManagerServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'modelsManager';

    /**
     * Register model manager service provider.
     *
     * @param mixed|null $parameter
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), function () {
            return new Manager();
        });
    }
}
