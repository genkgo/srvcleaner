<?php
namespace Genkgo\Srvcleaner\Util;

/**
 * Interface ProcessAwareInterface
 * @package Genkgo\Srvcleaner\Util
 */
interface ProcessAwareInterface
{
    /**
     * @param Processor $processor
     * @return void
     */
    public function setProcessor(Processor $processor);
}
