<?php

namespace Nilnice\Phalcon\Console\Command;

use Illuminate\Support\Str;
use Nilnice\Phalcon\Console\AbstractCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ControllerCommand extends AbstractCommand
{
    /**
     * @var string
     */
    private $name = 'app:controller';

    /**
     * @var string
     */
    private $description = 'Generate a new controller';

    /**
     * @var string
     */
    private $help = 'This command allows you to create a controller';

    /**
     * {@inheritdoc}
     */
    protected function handle()
    {
        $flysystem = flysystem($this->getControllerPath());
        $controller = $this->getControllerName();

        $this->info('The controller is generating...');

        if ($flysystem->has($controller)) {
            $name = $this->getInput()->getArgument('name');

            return $this->error("Controller [{$name}] already exists");
        }

        $stub = $this->getControllerStub();
        $content = static::replace($stub, [
            'namespace'  => 'App\Http\Controllers',
            'controller' => $this->getControllerName(false),
        ]);
        flysystem($this->getControllerPath())->put($controller, $content);

        $this->info($this->getControllerName(false) . ' has been generated.');

    }

    /**
     * @param string $subject
     * @param array  $array
     *
     * @return mixed|string
     */
    protected static function replace(string $subject, array $array)
    {
        foreach ($array as $key => $val) {
            $subject = str_replace('{' . $key . '}', $val, $subject);
        }

        return $subject;
    }

    protected function getControllerPath()
    {
        return di('application')->getBasePath() . 'app/Http/Controllers/';
    }

    /**
     * @param bool $isPath
     *
     * @return string
     */
    protected function getControllerName($isPath = true)
    {
        $name = $this->getInput()->getArgument('name');
        $name = Str::studly(Str::singular($name)) . 'Controller';

        return $isPath ? $name . '.php' : $name;
    }

    /**
     * @return bool|string
     */
    protected function getControllerStub()
    {
        return file_get_contents(__DIR__ . '/stubs/controller.stub');
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description)
            ->setHelp($this->help);

        $this->addArgument(
            'name',
            InputArgument::REQUIRED,
            'The controller name'
        );
    }
}
