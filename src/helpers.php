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
    function response(
        string $message = '',
        int $code = 200,
        array $headers = []
    ) {
        /** @var \Nilnice\Phalcon\Http\Response $response */
        $response = di('response');
        $response->setStatusCode($code);

        $content = [
            'code'    => $code,
            'message' => $message,
            'data'    => [],
        ];
        if ($headers) {
            foreach ($headers as $name => $value) {
                $response->setHeader($name, $value);
            }
        }
        $response->setJsonContent($content);

        return $response->send();
    }
}
