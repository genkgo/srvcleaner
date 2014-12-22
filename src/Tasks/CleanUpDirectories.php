<?php
namespace Genkgo\Srvcleaner\Tasks;

use CallbackFilterIterator;

/**
 * Class CleanUpDirectoriesTask
 * @package Genkgo\Srvcleaner\Tasks
 */
class CleanUpDirectories extends AbstractFilesystemCleanUp
{
    /**
     * @param $match
     * @return CallbackFilterIterator
     */
    protected function getList ($match) {
        $glob = new \GlobIterator($match);
        $filter = function (\SplFileInfo $item) {
            return $item->isDir();
        };

        return new CallbackFilterIterator($glob, $filter);
    }
}
