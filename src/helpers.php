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

if (! function_exists('response')) {
    function response(\Exception $e, $status = 200, array $headers = [])
    {
        if (config('app.debug')) {
            $data = [
                'code'          => $e->getCode(),
                'message'       => $e->getMessage(),
                'file'          => $e->getFile(),
                'line'          => $e->getLine(),
                'trace'         => $e->getTrace(),
                'traceAsString' => $e->getTraceAsString(),
            ];
        } else {
            $data = ['message' => $e->getMessage()];
        }
        $content = [
            'code'    => $e->getCode(),
            'message' => $e->getMessage(),
            'data'    => $data,
        ];

        if (di()->has('response')) {
            $response = di()->get('response');
        } else {
            $response = new \Nilnice\Phalcon\Http\Response();
        }

        if (method_exists($e, 'getStatusCode')) {
            $response->setStatusCode($e->getStatusCode());
        }

        $response->setJsonContent($content);

        return $response;
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
