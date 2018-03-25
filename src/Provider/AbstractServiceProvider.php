<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\DiInterface;
use Phalcon\Mvc\User\Component;

abstract class AbstractServiceProvider extends Component implements
    ServiceProviderInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * AbstractServiceProvider constructor.
     *
     * @param \Phalcon\DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        $this->setDI($di);
    }

    /**
     * Get the provider name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
