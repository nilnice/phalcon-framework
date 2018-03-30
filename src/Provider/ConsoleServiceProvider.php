<?php

namespace Nilnice\Phalcon\Provider;

use Symfony\Component\Console\Application;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    public const NAME = 'Artisan (c) for Phalcon Console';

    public const VERSION = 'v1.0.0';

    /**
     * @var string
     */
    protected $name = 'console';

    /**
     * @var array
     */
    private static $commands
        = [
            \Nilnice\Phalcon\Console\Command\ControllerCommand::class,
        ];

    /**
     * Register console service.
     *
     * @param mixed|null $parameter
     *
     * @return void
     */
    public function register($parameter = null): void
    {
        $definition = function () {
            $app = new Application(static::NAME, static::VERSION);

            if (PHP_SAPI === 'cli') {
                foreach (static::$commands as $command) {
                    $app->add(new $command);
                }
            }

            return $app;
        };
        $this->getDI()->setShared($this->getName(), $definition());
    }
}
