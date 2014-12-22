<?php
namespace Genkgo\Srvcleaner\Command;

use Genkgo\Srvcleaner\Config;
use Genkgo\Srvcleaner\TaskInterface;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CleanCommand
 * @package Genkgo\Srvcleaner\Command
 */
class CleanCommand extends AbstractCommand
{
    /**
     *
     */
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Clean up your server')
            ->addOption('config', '-c', InputOption::VALUE_REQUIRED, 'Configuration file');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = getcwd();
        $configFile = $input->getOption('config');
        if ($configFile === null) {
            $configFile = $cwd.'/srvcleaner.json';
        }

        $config = Config::fromFile($configFile);
        $tasks = $config->getTasks();
        $tasks->each(function (TaskInterface $task) use ($cwd) {
            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor(new Processor());
            }
            $task->setCurrentWorkingDirectory($cwd);
            $task->execute();
        });
    }
}
