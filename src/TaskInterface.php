<?php
namespace Genkgo\Srvcleaner;

use stdClass;

/**
 * Interface TaskInterface
 * @package Genkgo\Srvcleaner
 */
interface TaskInterface
{
    /**
     * @param $directory
     */
    public function setCurrentWorkingDirectory($directory);

    /**
     * @param stdClass $config
     */
    public function setConfig(stdClass $config);

    /**
     * @return stdClass
     */
    public function getConfig();

    /**
     */
    public function execute();
}
