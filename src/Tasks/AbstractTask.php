<?php
namespace Genkgo\Srvcleaner\Tasks;

use Genkgo\Srvcleaner\TaskInterface;
use stdClass;

/**
 * Class AbstractTask
 * @package Genkgo\Srvcleaner\Tasks
 */
abstract class AbstractTask implements TaskInterface
{
    /**
     * @var
     */
    private $currentWorkingDirectory;

    /**
     * @var stdClass
     */
    private $config;

    /**
     * @return stdClass
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param stdClass $config
     */
    public function setConfig(stdClass $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getCurrentWorkingDirectory()
    {
        return $this->currentWorkingDirectory;
    }

    /**
     * @param string $currentWorkingDirectory
     */
    public function setCurrentWorkingDirectory($currentWorkingDirectory)
    {
        $this->currentWorkingDirectory = $currentWorkingDirectory;
    }

    /**
     * @param bool $dryRun
     * @return void
     */
    abstract public function execute($dryRun = false);
}
