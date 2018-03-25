<?php

namespace Nilnice\Phalcon\Provider;

use Nilnice\Phalcon\Event\DatabaseEvent;
use Phalcon\Db\Adapter\Pdo\Mysql;

class DatabaseServiceProvider extends AbstractServiceProvider
{
    /**
     * Register database service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        ['name' => $name, 'item' => $item] = $parameter;
        $this->name = $name;
        $di = $this->getDI();

        $di->setShared($this->getName(), function () use ($di, $name, $item) {
            $mysql = new Mysql($item);

            if (! env('DB_LISTEN')) {
                /** @var \Phalcon\Events\Manager $manager */
                $manager = $di->getShared('eventsManager');
                $manager->attach($name, new DatabaseEvent());
                $mysql->setEventsManager($manager);
            }

            return $mysql;
        });
    }
}
