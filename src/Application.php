<?php

namespace Nilnice\Phalcon;

use Illuminate\Support\Str;
use Nilnice\Phalcon\Exception\ErrorHandler;
use Nilnice\Phalcon\Provider\ConfigServiceProvider;
use Nilnice\Phalcon\Provider\ConsoleServiceProvider;
use Nilnice\Phalcon\Provider\DatabaseServiceProvider;
use Nilnice\Phalcon\Provider\DispatcherServiceProvider;
use Nilnice\Phalcon\Provider\ErrorHandleProvider;
use Nilnice\Phalcon\Provider\EventManagerServiceProvider;
use Nilnice\Phalcon\Provider\MetadataServiceProvider;
use Nilnice\Phalcon\Provider\ModelManagerServiceProvider;
use Nilnice\Phalcon\Provider\MonologServiceProvider;
use Nilnice\Phalcon\Provider\RedisServiceProvider;
use Nilnice\Phalcon\Provider\RequestServiceProvider;
use Nilnice\Phalcon\Provider\ResponseServiceProvider;
use Nilnice\Phalcon\Provider\RouterServiceProvider;
use Nilnice\Phalcon\Provider\SecurityServiceProvider;
use Nilnice\Phalcon\Provider\ServiceProviderInterface;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\DiInterface;

class Application
{
    /**
     * @var \Phalcon\Mvc\Application
     */
    protected $app;

    /**
     * @var \Phalcon\DiInterface
     */
    protected $di;
    /**
     * @var string|null
     */
    protected $basePath;

    /**
     * @var array
     */
    protected $providers = [];

    /**
     * @var array
     */
    protected $loadedConfigurations = [];

    /**
     * Application constructor.
     *
     * @param string|null $basePath
     */
    public function __construct(string $basePath = null)
    {
        ErrorHandler::register();
        $this->basePath = $basePath;
        $this->di = new Di();
        $this->di->setShared('application', $this);
        Di::setDefault($this->di);

        $this->loadProvider();

        $this->app = new \Phalcon\Mvc\Application();
        $this->app->setDI($this->di);
        $this->app->useImplicitView(false);
    }

    /**
     * Get base path.
     *
     * @param string|null $path
     *
     * @return string|null
     */
    public function getBasePath(string $path = null): ?string
    {
        if ($this->basePath !== null) {
            return $this->basePath . $path;
        }

        if ($this->isRunningInConsole()) {
            $this->basePath = getcwd();
        } else {
            $this->basePath = \dirname(getcwd() . '/../');
        }

        return $this->getBasePath($path);
    }

    /**
     * Get storage path.
     *
     * @param string|null $path
     *
     * @return string
     */
    public function getStoragePath(string $path = null): string
    {
        return $this->getBasePath() . 'storage/' . ($path ? $path . '/' : '');
    }

    /**
     * @param string|null $name
     *
     * @return string|null
     */
    public function getConfigPath(string $name = null): ?string
    {
        if (! $name) {
            if (file_exists($path = $this->getBasePath('config') . '/')) {
                return $path;
            }

            if (file_exists($path = __DIR__ . '/../config/')) {
                return $path;
            }
        }

        if (file_exists(
            $path = $this->getBasePath('config' . '/' . $name . '.php'))
        ) {
            return $path;
        }

        if (file_exists($path = __DIR__ . '/../config/' . $name . '.php')) {
            return $path;
        }
    }

    /**
     * @return \Phalcon\Mvc\Application
     */
    public function getApp(): \Phalcon\Mvc\Application
    {
        return $this->app;
    }

    /**
     * @return \Phalcon\DiInterface
     */
    public function getDi(): DiInterface
    {
        return $this->di;
    }

    /**
     * @return void
     */
    public function run(): void
    {
        if ($this->app instanceof \Phalcon\Mvc\Application) {
            $this->output();
        }
    }

    /**
     * @return bool
     */
    public function isRunningInConsole(): bool
    {
        return PHP_SAPI === 'cli';
    }

    /**
     * Output response content.
     *
     * @return void
     */
    public function output(): void
    {
        try {
            $this->app->handle();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }

        $response = $this->app->response;

        if (! $response->isSent()) {
            $response->send();
        }
    }

    /**
     * Initialize the Service in the Dependency Injector Container.
     *
     * @param \Nilnice\Phalcon\Provider\ServiceProviderInterface $provider
     *
     * @return \Nilnice\Phalcon\Application
     */
    public function register(ServiceProviderInterface $provider): self
    {
        if (\get_class($provider) === DatabaseServiceProvider::class) {
            $this->configure('database');

            /** @var \Phalcon\Config $config */
            $config = $this->getDi()->getShared('config');

            /** @var \Phalcon\Config $database */
            if ($database = $config->get('database')) {
                $connections = $database->get('connections')->toArray();
                foreach ($connections as $dbname => $item) {
                    $name = Str::camel($dbname);
                    $provider->register(['name' => $name, 'item' => $item]);
                    $this->providers[$provider->getName()] = $provider;
                }
            }
        } else {
            $provider->register();

            if ($name = $provider->getName()) {
                $this->providers[$name] = $provider;
            }
        }

        return $this;
    }

    /**
     * @param string $name
     */
    public function configure(string $name): void
    {
        if (isset($this->loadedConfigurations[$name])) {
            return;
        }

        $this->loadedConfigurations[$name] = true;
        $path = $this->getConfigPath($name);

        if ($path) {
            $value = new Config([$name => require $path]);

            /** @var \Phalcon\Config $config */
            $config = $this->getDi()->getShared('config');
            $config->merge($value);
        }
    }

    /**
     * Load service provider.
     *
     * @return void
     */
    private function loadProvider(): void
    {
        $providers = [
            ConfigServiceProvider::class,
            ErrorHandleProvider::class,
            MonologServiceProvider::class,
            ConsoleServiceProvider::class,
            RouterServiceProvider::class,
            EventManagerServiceProvider::class,
            DispatcherServiceProvider::class,
            DatabaseServiceProvider::class,
            MetadataServiceProvider::class,
            ModelManagerServiceProvider::class,
            RequestServiceProvider::class,
            ResponseServiceProvider::class,
            SecurityServiceProvider::class,
            RedisServiceProvider::class,
        ];
        foreach ($providers as $provider) {
            $this->register(new $provider($this->getDi()));
        }
    }
}
