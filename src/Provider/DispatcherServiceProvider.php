<?php

namespace Nilnice\Phalcon\Provider;

use Nilnice\Phalcon\Exception\HttpException;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;

class DispatcherServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'dispatcher';

    /**
     * Register dispatcher service provider.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () {
            $route = config('app.route');

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace($route->get('namespace'));
            $dispatcher->setDefaultController($route->get('controller'));
            $dispatcher->setActionName($route->get('action'));

            return $dispatcher;
        });

        /** @var \Phalcon\Events\Manager $manager */
        $manager = $di->getShared('eventsManager');
        $manager->attach('dispatch:beforeException', function (
            Event $event,
            Dispatcher $dispatcher,
            \Exception $e
        ) {
            if (config('app.debug')) {
                $params = [
                    'code'          => $e->getCode(),
                    'message'       => $e->getMessage(),
                    'file'          => $e->getFile(),
                    'line'          => $e->getLine(),
                    'traceAsString' => $e->getTraceAsString(),
                ];
            } else {
                $params = ['message' => $e->getMessage()];
            }
            $notFound = [
                'controller' => 'NotFound',
                'action'     => 'notFound',
                'params'     => $params,
            ];

            if ($e instanceof DispatcherException) {
                $dispatcher->forward($notFound);

                return false;
            }

            switch ($e->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
                    $dispatcher->forward($notFound);

                    return false;
            }
        });

        $dispatcher = $di->getShared($this->getName());
        $dispatcher->setEventsManager($manager);
    }
}
