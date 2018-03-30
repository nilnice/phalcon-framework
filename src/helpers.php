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
     * @return \Nilnice\Phalcon\Application
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
    function config($option = null, $default = false)
    {
        /** @var \Phalcon\Config $config */
        $config = di('config');

        if ($option === null) {
            return $config;
        }

        if (is_array($option)) {
            if ($default) {
                $config->merge(new Config(['app' => $option]));
                $array = $config->toArray();
            } else {
                $array = array_replace_recursive(
                    $config ? $config->toArray() : [],
                    ['app' => $option]
                );
            }

            di()->setShared('config', function () use ($array) {
                return new Config($array);
            });

            return true;
        }

        if (is_string($option)) {
            return $config->path($option);
        }
    }
}

if (! function_exists('flysystem')) {
    function flysystem(string $path = null)
    {
        if (null !== $path) {
            $adapter = new \League\Flysystem\Adapter\Local($path);

            return new \League\Flysystem\Filesystem($adapter);
        }

        return di()->get('flysystem');
    }
}

if (! function_exists('storage_path')) {
    /**
     * @param null $path
     *
     * @return string|null
     */
    function storage_path($path = null)
    {
        return app()->getStoragePath($path);
    }
}
