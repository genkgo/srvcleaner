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
     * @return mixed
     */
    public function setProcessor(Processor $processor);
}
