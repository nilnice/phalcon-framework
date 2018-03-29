<?php

namespace Nilnice\Phalcon\Console;

use Symfony\Component\Console\Command\Command;

abstract class Console extends Command
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
     * @return mixed
     */
    protected function getInput()
    {
        return $this->input;
    }

    /**
     * @return mixed
     */
    protected function getOutput()
    {
        return $this->output;
    }
}
