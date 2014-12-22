<?php
namespace Genkgo\Srvcleaner\Tasks;

use DateTime;
use DateInterval;
use SplFileInfo;
use DirectoryIterator;
use CallbackFilterIterator;
use Genkgo\Srvcleaner\Exceptions\ConfigurationException;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;

/**
 * Class CleanUpDirectoriesTask
 * @package Genkgo\Srvcleaner\Tasks
 */
abstract class AbstractFilesystemCleanUp extends AbstractTask implements ProcessAwareInterface
{
    /**
     * @var Processor
     */
    private $processor;

    /**
     * @param Processor $processor
     * @return void
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

        if (!isset($this->getConfig()->path) || !isset($this->getConfig()->match)) {
            throw new ConfigurationException('Config `path` and `match` are required to cleanup directories');
        }

        $path = $this->getConfig()->path;
        $match = $this->getConfig()->match;
        if (isset($this->getConfig()->recursive) && $this->getConfig()->recursive === true) {
            $recursive = true;
        } else {
            $recursive = false;
        }

        $shouldBeRemoved = $this->getListForRemoval($path, $match, $recursive);
        foreach ($shouldBeRemoved as $item) {
            if (file_exists($item->getPathname())) {
                $this->processor->execute("rm -Rf {$item->getPathname()}");
            }
        }
    }

    /**
     * @param string $match
     * @return CallbackFilterIterator
     */
    abstract protected function getList ($match);

    /**
     * @param $path
     * @param array $matches
     * @param bool $recursive
     * @return SplFileInfo[]
     */
    private function getListForRemoval ($path, array $matches, $recursive = false) {
        $scheduleForRemoval = [];

        foreach ($matches as $match) {
            $list = $this->getList($path . '/' . $match);
            foreach ($list as $item) {
                if ($this->filter($item)) {
                    $scheduleForRemoval[] = $item;
                }
            }
        }

        if ($recursive) {
            $scheduleForRemoval = $this->getRecursiveListForRemoval($path, $matches, $recursive, $scheduleForRemoval);
        }

        return $scheduleForRemoval;
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
                $compareDate->sub(new DateInterval($configValue));
                $timeCleanup = $compareDate->format('U');

                $time = call_user_func([$item, 'get' . $getter]);
                if ($timeCleanup < $time) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * @param $path
     * @param array $matches
     * @param $recursive
     * @param $scheduleForRemoval
     * @return array
     */
    private function getRecursiveListForRemoval($path, array $matches, $recursive, $scheduleForRemoval)
    {
        $dir = new DirectoryIterator($path);
        foreach ($dir as $file) {
            if ($file->isDir() && !$file->isDot()) {
                $scheduleForRemoval = array_merge(
                    $scheduleForRemoval,
                    $this->getListForRemoval(
                        $file->getPathname(),
                        $matches,
                        $recursive
                    )
                );
            }
        }
        return $scheduleForRemoval;
    }
}
