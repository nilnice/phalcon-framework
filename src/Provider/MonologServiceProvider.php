<?php

namespace Nilnice\Phalcon\Provider;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use Symfony\Component\Filesystem\Filesystem;

class MonologServiceProvider extends AbstractServiceProvider
{
    /**
     * @var string
     */
    protected $name = 'monolog';

    /**
     * Register monolog service.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $di = $this->getDI();
        $di->setShared($this->getName(), function () {
            $app = config('app.log.app');

            switch ($app->get('mode')) {
                case 'yearly':
                    $format = sprintf('/%s/%s/%s/',
                        date('Y'),
                        date('m'),
                        date('d')
                    );
                    break;
                case 'monthly':
                    $format = sprintf('/%s/%s/', date('Y'), date('m'));
                    break;
                default:
                    $format = '';
                    break;
            }
            $path = storage_path('logs') . $format;

            if (! file_exists($path)) {
                $filesystem = new Filesystem();
                $filesystem->mkdir($path);
            }
            $filename = $path . $app->get('name');

            $logger = new Logger($filename);
            $formatter = new LineFormatter(null, null, false, true);
            $handler = new RotatingFileHandler($filename, $app->get('max'));
            $handler->setFilenameFormat(
                $app->get('file_format'),
                $app->get('date_format')
            );
            $handler->setFormatter($formatter);
            $logger->pushHandler($handler);

            return $logger;
        });
    }
}
