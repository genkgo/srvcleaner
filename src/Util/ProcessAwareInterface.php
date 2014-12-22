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
     */
    public function setProcessor(Processor $processor);
}
