<?php

namespace Nilnice\Phalcon\Provider;

use Symfony\Component\Console\Application;

class ConsoleServiceProvider extends AbstractServiceProvider
{
    public const VERSION = 'v1.0.0';

    public const DESCRIPTION = 'Artisan (c) for Phalcon Console';

    /**
     * @var string
     */
    protected $name = 'console';

    /**
     * @var array
     */
    private static $commands
        = [
            \Nilnice\Phalcon\Console\App\ControllerCommand::class,
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
        $this->getDI()->set($this->getName(), function () {
            $app = new Application($this->getName(), static::VERSION);

            if (PHP_SAPI === 'cli') {
                foreach (static::$commands as $command) {
                    $app->add(new $command);
                }
            }

            return $app;
        });
    }
}
