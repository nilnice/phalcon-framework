<?php

namespace Nilnice\Phalcon\Provider;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\MountManager;

class FlysystemServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'flysystem';

    /**
     * Register flysystem service.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $manager = $this->getMountManager();
        $this->getDI()->setShared($this->getName(), function () use ($manager) {
            return $manager;
        });
        // $manager->getFilesystem(config('app.flysystem'));
    }

    /**
     * Get mount manager.
     *
     * @return \League\Flysystem\MountManager
     *
     * @throws \InvalidArgumentException
     */
    protected function getMountManager(): MountManager
    {
        $files = [];
        $flysystems = config('flysystem');

        foreach ($flysystems as $key => $flysystem) {
            switch ($key) {
                case 'local':
                    $adapter = $this->getLocalAdapter($flysystem->toArray());
                    break;
            }
            $files[$key] = new Filesystem($adapter);
        }

        return new MountManager($files);
    }

    /**
     * Get flysystem local adapter.
     *
     * @param array $flysystem
     *
     * @return \League\Flysystem\AdapterInterface
     */
    protected function getLocalAdapter(array $flysystem)
    {
        $class = $flysystem['class'];

        /** @var \Nilnice\Phalcon\Application $app */
        $app = $this->getDI()->getShared('application');

        return new $class($app->getBasePath());
    }
}
