<?php
namespace Genkgo\Srvcleaner\Console;

use Genkgo\Srvcleaner\Command\CleanCommand;
use Genkgo\Srvcleaner\Srvcleaner;
use Symfony\Component\Console\Application as BaseApplication;

/**
 * Class Application
 * @package Genkgo\Srvcleaner\Console
 */
class Application extends BaseApplication
{
    /**
     *
     */
    public function __construct()
    {
        parent::__construct('Srvcleaner', Srvcleaner::VERSION);
    }

    /**
     * Initializes all the composer commands
     */
    protected function getDefaultCommands()
    {
        $commands = parent::getDefaultCommands();
        $commands[] = new CleanCommand();
        return $commands;
    }
}
