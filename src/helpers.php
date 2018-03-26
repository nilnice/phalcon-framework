<?php

use Phalcon\Config;
use Phalcon\Di;

if (! function_exists('di')) {
    /**
     * @param string|null $name
     *
     * @return mixed|\Phalcon\DiInterface
     */
    function di(string $name = null)
    {
        $di = Di::getDefault();

        if ($name === null) {
            return $di;
        }

        return $di->get($name);
    }
}

if (! function_exists('app')) {
    /**
     * @return \Phalcon\Mvc\Application
     */
    function app()
    {
        return di()->get('application');
    }
}

if (! function_exists('config')) {
    /**
     * @param mixed|null $option
     * @param mixed|null $default
     *
     * @return bool|mixed|\Phalcon\Config
     */
    function config($option = null, $default = null)
    {
        /** @var \Phalcon\Config $config */
        $config = di()->get('config')->get('config');

        if ($option === null) {
            return $config;
        }

        if (is_array($option)) {
            if ($default) {
                $config->merge(new Config($option));
                $array = $config->toArray();
            } else {
                $array = array_replace_recursive(
                    $config ? $config->toArray() : [],
                    $option
                );
            }

            di()->set('config', function () use ($array) {
                return new Config($array);
            });

            return true;
        }

        if (is_string($option)) {
            return $config->path($option);
        }
    }
}
