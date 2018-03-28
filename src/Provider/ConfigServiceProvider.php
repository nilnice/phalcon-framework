<?php

namespace Nilnice\Phalcon\Provider;

use Phalcon\Config;
use Phalcon\DiInterface;
use Symfony\Component\Finder\Finder;

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
        $name = $this->getName();
        $di->setShared($name, function () use ($di, $name) {
            /** @var \Nilnice\Phalcon\Application $app */
            $app = $di->getShared('application');
            $dirs = $app->getBasePath() . '/config';

            $finder = new Finder();
            $finder->files()
                ->ignoreDotFiles(true)
                ->name('/^[a-z_]+\.php$/')
                ->in($dirs);
            $array = [];
            foreach ($finder as $item) {
                $name = $item->getBasename('.php');
                $array[$name] = require $item;
            }
            $config = new Config($array);

            return $config;
        });
    }
}
