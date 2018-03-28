<?php

namespace Nilnice\Phalcon\Provider;

class CacheServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'cache';

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
            $backend = $adapter->get('backend');
            $frontend = $adapter->get('frontend');

            $cache = new $frontend([
                'lifetime' => $adapter->get('lifetime'),
            ]);
            $options = $adapter->get('options')->toArray();

            return new $backend($cache, $options);
        });

        /** @var \Phalcon\Cache\Backend\Redis $redis */
        $redis = $this->getDI()->getShared($this->getName());
        $redis->save('hello', [1, 2, 3, 4, 5]);
    }

    /**
     * @return bool|mixed|\Phalcon\Config
     */
    private function getAdapterName()
    {
        return config('app.cache_adapter');
    }
}
