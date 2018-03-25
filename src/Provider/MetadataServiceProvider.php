<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Mvc\Model\MetaData\Memory;

class MetadataServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'modelsMetadata';

    /**
     * @param string|null $parameter
     */
    public function register($parameter = null): void
    {
        $this->getDI()->setShared($this->getName(), function () {
            return new Memory();
        });
    }
}
