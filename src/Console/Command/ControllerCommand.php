<?php

namespace Nilnice\Phalcon\Console\App;

use Symfony\Component\Console\Command\Command;

class ControllerCommand extends Command
{
    /**
     * @var string
     */
    protected $name = 'app:controller';

    protected $description = 'Generate a new controller';
}
