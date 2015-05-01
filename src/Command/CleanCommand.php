<?php
namespace Genkgo\Srvcleaner\Command;

use Genkgo\Srvcleaner\Config;
use Genkgo\Srvcleaner\TaskInterface;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Logger\ConsoleLogger;
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
            ->addOption('config', '-c', InputOption::VALUE_REQUIRED, 'Configuration file')
            ->addOption('dry-run', '-c', InputOption::VALUE_NONE, 'Do nothing, show what is to be cleaned');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = getcwd();
        $configFile = $input->getOption('config');
        if ($configFile === null) {
            $configFile = $cwd.'/srvcleaner.json';
        }

        $logger = new ConsoleLogger($output, [
            LogLevel::EMERGENCY => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::ALERT => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::CRITICAL => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::ERROR => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::WARNING => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::NOTICE => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::INFO => OutputInterface::VERBOSITY_NORMAL,
            LogLevel::DEBUG => OutputInterface::VERBOSITY_VERBOSE,
        ]);

        $config = Config::fromFile($configFile);
        $tasks = $config->getTasks();
        $tasks->each(function (TaskInterface $task) use ($cwd, $input, $logger) {
            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor(new Processor());
            }
            if ($task instanceof LoggerAwareInterface) {
                $task->setLogger($logger);
            }
            $task->setCurrentWorkingDirectory($cwd);
            $task->execute($input->getOption('dry-run'));
        });
    }
}
