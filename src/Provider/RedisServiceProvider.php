<?php

namespace Nilnice\Phalcon\Provider;

class RedisServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'redis';

    /**
     * Register cache service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $name = $this->getAdapterName();
        $this->getDI()->setShared($this->getName(), function () use ($name) {
            $adapter = config('cache.connections.' . $name);
            $array = $adapter->toArray();
            $driver = $array['driver'];
            $options = $array[$driver]['options'];

            if ($driver === 'default') {
                $frontend = $array[$driver]['frontend'];
                $backend = $array[$driver]['backend'];
                $cache = new $frontend([
                    'lifetime' => $array[$driver]['lifetime'],
                ]);

                return new $backend($cache, $options);
            } else {
                $servers = $array[$driver]['servers'];
                $client = $array[$driver]['client'];

                return new $client($servers, [
                    'cluster'    => 'redis',
                    'parameters' => $options,
                ]);
            }
        });
    }

    /**
     * @return bool|mixed|\Phalcon\Config
     */
    private function getAdapterName()
    {
        return config('app.cache_adapter');
    }
}
