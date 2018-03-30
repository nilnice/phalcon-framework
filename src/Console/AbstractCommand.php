<?php

namespace Nilnice\Phalcon\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    /**
     * @var \Symfony\Component\Console\Input\InputInterface
     */
    private $input;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    /**
     * @param string $message
     */
    public function info(string $message)
    {
        $this->getOutput()->writeln("<info>$message</info>");
    }

    /**
     * @param string $message
     */
    public function error(string $message)
    {
        $this->getOutput()->writeln("<error>$message</error>");
    }

    /**
     * @param string $message
     */
    public function echo(string $message)
    {
        $this->getOutput()->writeln($message);
    }

    /**
     * @param string $message
     */
    public function comment(string $message)
    {
        $this->getOutput()->writeln("<comment>$message</comment>");
    }

    /**
     * An abstract method that will be called on every concrete command.
     *
     * @return mixed
     */
    abstract protected function handle();

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->handle();
    }

    /**
     * @return \Symfony\Component\Console\Input\InputInterface
     */
    public function getInput(): InputInterface
    {
        return $this->input;
    }

    /**
     * @return \Symfony\Component\Console\Output\OutputInterface
     */
    public function getOutput(): OutputInterface
    {
        return $this->output;
    }
}
