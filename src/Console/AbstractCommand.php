<?php

namespace Nilnice\Phalcon\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $help;

    /**
     * @var mixed
     */
    protected $input;

    /**
     * @var mixed
     */
    protected $output;

    /**
     * An abstract method that will be called on every concrete command.
     *
     * @return mixed
     */
    abstract protected function goExecute();

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->goExecute();
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    protected function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    protected function getOutput(): OutputInterface
    {
        return $this->output;
    }

    /**
     * This provides the arguments of this command.
     *
     * @return array
     */
    protected function getArgument()
    {
        return [];
    }

    /**
     * This provides the options of this command.
     *
     * @return array
     */
    protected function getOption()
    {
        return [];
    }

    protected function configure()
    {
        $this->setName($this->name)
            ->setDescription($this->description);

        if ($this->help) {
            $this->setHelp($this->help);
        }

        if (! empty($arguments = $this->getArgument())) {
            foreach ($arguments as $argument) {
                [0 => $name, 1 => $mode, 2 => $description] = $argument;
                $default = $argument[3] ?? null;
                $this->addArgument($name, $mode, $description, $default);
            }
        }

        if (! empty($options = $this->getOption())) {
            foreach ($options as $option) {
                $this->addOption(
                    $option[0] ?? null,
                    $option[1] ?? null,
                    $option[2] ?? null,
                    $option[3] ?? null,
                    $option[4] ?? null
                );
            }
        }

        return $this;
    }
}
