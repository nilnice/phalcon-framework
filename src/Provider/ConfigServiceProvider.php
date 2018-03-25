<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Config;
use Phalcon\DiInterface;

class ConfigServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'config';

    /**
     * Register configuration service.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () use ($di) {
            /** @var \Nilnice\Phalcon\Application $app */
            $app = $di->getShared('application');
            $filename = $app->getBasePath() . '/config/app.php';
            $config = null;

            if (file_exists($filename)) {
                $config = require $filename;
            }

            if (\is_array($config)) {
                $config = new Config($config);
            }

            return $config;
        });
    }
}
