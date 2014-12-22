<?php
namespace Genkgo\Srvcleaner\Util;

use Genkgo\Srvcleaner\Exceptions\ProcessException;
use Symfony\Component\Process\Process;

/**
 * Class Processor
 * @package Genkgo\Srvcleaner\Util
 */
class Processor
{
    /**
     * @var
     */
    private $currentWorkingDirectory;

    /**
     * @return mixed
     */
    public function getCurrentWorkingDirectory()
    {
        return $this->currentWorkingDirectory;
    }

    /**
     * @param mixed $currentWorkingDirectory
     */
    public function setCurrentWorkingDirectory($currentWorkingDirectory)
    {
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    /**
     * @param $command
     */
    public function execute($command)
    {
        $cwd = getcwd();
        chdir($this->currentWorkingDirectory);
        $process = new Process($command);
        $process->enableOutput();
        $process->run();
        if (!$process->isSuccessful()) {
            chdir($cwd);
            throw new ProcessException($process->getErrorOutput());
        }
        chdir($cwd);
    }
}
