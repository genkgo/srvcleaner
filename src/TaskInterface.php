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
     * @param string $directory
     * @return void
     */
    public function setCurrentWorkingDirectory($directory);

    /**
     * @param stdClass $config
     * @return void
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
