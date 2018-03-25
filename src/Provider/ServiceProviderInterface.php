<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Di\InjectionAwareInterface;

interface ServiceProviderInterface extends InjectionAwareInterface
{
    /**
     * Register service provider.
     *
     * @param string $parameter
     */
    public function register($parameter = null): void;

    /**
     * Get service provider name.
     *
     * @return null|string
     */
    public function getName(): ?string;
}
