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
     * @var
     */
    private $config;

    /**
     * @return mixed
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     */
    public function setConfig(stdClass $config)
    {
        $this->config = $config;
    }

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
     */
    abstract public function execute();
}
