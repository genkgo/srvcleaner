<?php
namespace Genkgo\Srvcleaner\Tasks;

use CallbackFilterIterator;

/**
 * Class CleanUpDirectoriesTask
 * @package Genkgo\Srvcleaner\Tasks
 */
class CleanUpDirectories extends AbstractFilesystemCleanUp
{
    protected function getList () {
        $path = $this->getConfig()->path;
        $glob = new \GlobIterator($path);
        $filter = function (\SplFileInfo $item) {
            return $item->isDir();
        };

        return new CallbackFilterIterator($glob, $filter);
    }
}
