<?php
namespace Genkgo\Srvcleaner\Tasks;

use CallbackFilterIterator;

/**
 * Class CleanUpDirectoriesTask
 * @package Genkgo\Srvcleaner\Tasks
 */
class CleanUpFiles extends AbstractFilesystemCleanUp
{
    protected function getList () {
        $path = $this->getConfig()->path;
        $glob = new \GlobIterator($path);
        $filter = function (\SplFileInfo $item) {
            return $item->isFile();
        };

        return new CallbackFilterIterator($glob, $filter);
    }
}
