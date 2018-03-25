<?php

namespace Nilnice\Phalcon\Provider;

class RouterServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'router';

    /**
     * Register router service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $definition = function () use ($di) {
            /** @var \Nilnice\Phalcon\Application $app */
            $app = $di->getShared('application');

            return require $app->getBasePath() . 'routes/api.php';
        };
        $di->setShared($this->getName(), $definition());
    }
}
