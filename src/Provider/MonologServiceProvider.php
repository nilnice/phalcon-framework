<?php

namespace Nilnice\Phalcon\Provider;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class MonologServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'monolog';

    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () {
            $framework = config('app.log.framework');

            switch ($framework->get('mode')) {
                case 'yearly':
                    $mode = date('Y');
                    break;
                case 'monthly':
                    $mode = date('Ym');
                    break;
                default:
                    $mode = date('Ymd');
                    break;
            }
            $name = $framework->get('name') . '-' . $mode;
            $logger = new Logger($name);

            $filename = storage_path('storage/framework/') . $name;
            $maxFiles = $framework->get('max');
            $handler = new RotatingFileHandler($filename, $maxFiles);
            $formatter = new LineFormatter(null, null, true, true);
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            return $logger;
        });
        /** @var Logger $logger */
        $logger = $di->getShared($this->getName());
    }
}
