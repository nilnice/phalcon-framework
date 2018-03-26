<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Security;

class SecurityServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'security';

    /**
     * Register security service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () {
            return new Security();
        });
    }
}
