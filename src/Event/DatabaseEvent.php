<?php

namespace Nilnice\Phalcon\Event;

use Phalcon\Db\Adapter\Pdo;
use Phalcon\Db\Profiler;
use Phalcon\DiInterface;
use Phalcon\Events\Event;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\File;
use Symfony\Component\Filesystem\Filesystem;

class DatabaseEvent
{
    /**
     * @var string|null
     */
    protected $filename;

    /**
     * @var \Phalcon\Db\Profiler
     */
    protected $profiler;

    /**
     * @var \Phalcon\Logger\Adapter\File
     */
    protected $logger;

    /**
     * DatabaseEvent constructor.
     *
     * @param string|null $filename
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    public function __construct(DiInterface $di, string $filename = null)
    {
        $this->di = $di;
        $this->filename = $filename;
        $this->profiler = new Profiler();
        $this->logger = new File($this->getLogFile());
    }

    /**
     * This is executed if the event triggered is 'beforeQuery'.
     *
     * @param \Phalcon\Events\Event   $event
     * @param \Phalcon\Db\Adapter\Pdo $connection
     *
     * @return void
     */
    public function beforeQuery(Event $event, Pdo $connection): void
    {
        dd($event->getSource(), $event->getData(), $event->getType(),
            $connection);
        if (__FUNCTION__ !== $event->getType()) {
            return;
        }
        $this->profiler->startProfile($connection->getSQLStatement());
    }

    /**
     * This is executed if the event triggered is 'afterQuery'.
     *
     * @param \Phalcon\Events\Event   $event
     * @param \Phalcon\Db\Adapter\Pdo $connection
     *
     * @return void
     */
    public function afterQuery(Event $event, Pdo $connection): void
    {
        if (__FUNCTION__ !== $event->getType()) {
            return;
        }

        $Line = new Logger\Formatter\Line("[%date%] - [%type%]\r\n%message%");
        $this->logger->setFormatter($Line);
        $this->logger->log($connection->getSQLStatement(), Logger::INFO);
        $this->profiler->stopProfile();
    }

    /**
     * @return \Phalcon\Db\Profiler
     */
    public function getProfiler(): Profiler
    {
        return $this->profiler;
    }

    /**
     * @return string
     *
     * @throws \Symfony\Component\Filesystem\Exception\IOException
     */
    protected function getLogFile(): string
    {
        $path = storage_path('database') . '%s/%s/%s/' . $this->filename;
        $path = sprintf($path, date('Y'), date('m'), date('d'));

        if (! file_exists($path)) {
            $filesystem = new Filesystem();
            $filesystem->mkdir(\dirname($path));
        }

        return $path;
    }
}
