<?php
namespace Genkgo\Srvcleaner\Tasks;

use DateTime;
use Genkgo\Srvcleaner\Exceptions\ConfigurationException;
use SplFileInfo;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;

/**
 * Class CleanUpDirectoriesTask
 * @package Genkgo\Srvcleaner\Tasks
 */
class CleanUpDirectories extends AbstractTask implements ProcessAwareInterface
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @param Processor $processor
     */
    public function setProcessor(Processor $processor)
    {
        $this->processor = $processor;
    }

    /**
     *
     */
    public function execute()
    {
        $this->processor->setCurrentWorkingDirectory($this->getCurrentWorkingDirectory());

        $scheduleForDeletions = [];
        if (!isset($this->getConfig()->path)) {
            throw new ConfigurationException('The config `path` is required to cleanup directories');
        }
        $path = $this->getConfig()->path;
        $iterator = new \GlobIterator($path);
        foreach ($iterator as $item) {
            if ($item->isDir()) {
                if ($this->filter($item)) {
                    $scheduleForDeletions[] = $item;
                }
            }
        }

        foreach ($scheduleForDeletions as $item) {
            /* @var $item SplFileInfo */
            $this->processor->execute("rm -Rf {$item->getPathname()}");
        }
    }

    /**
     * @param SplFileInfo $item
     * @return bool
     */
    private function filter(SplFileInfo $item)
    {
        $intervalBased = [
            'accessAt' => 'ATime',
            'modifiedAt' => 'MTime'
        ];
        foreach ($intervalBased as $property => $getter) {
            if (isset($this->getConfig()->{$property})) {
                $configValue = $this->getConfig()->{$property};
                $compareDate = new DateTime('now');
                $compareDate->sub(new \DateInterval($configValue));
                $timeCleanup = $compareDate->format('U');

                $time = call_user_func([$item, 'get' . $getter]);
                if ($timeCleanup < $time) {
                    return false;
                }
            }
        }
        return true;
    }
}
