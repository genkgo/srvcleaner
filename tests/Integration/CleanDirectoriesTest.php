<?php
namespace Genkgo\Srvcleaner\Integration;

use Genkgo\Srvcleaner\AbstractTestCase;
use Genkgo\Srvcleaner\Config;
use Genkgo\Srvcleaner\TaskInterface;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;

class CleanDirectoriesTest extends AbstractTestCase
{
    private $tmpDir;

    protected function setUp()
    {
        $this->tmpDir = sys_get_temp_dir().'/srvcleaner'.uniqid();
        mkdir($this->tmpDir);
    }


    public function testRemoveDirectories()
    {
        $config = Config::fromFile(__DIR__ .'/config/config-clean-directories.json');
        $tasks = $config->getTasks();

        $this->assertCount(1, $tasks);
        $this->assertContainsOnlyInstancesOf(TaskInterface::class, $tasks);

        $processor = $this->getMock(Processor::class);
        $processor->expects($this->once())->method('setCurrentWorkingDirectory')->with(
            $this->equalTo(dirname(__DIR__))
        );
        $processor->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$this->tmpDir}")
        );

        $tasks->each(function (TaskInterface $task, $name) use ($processor) {
            $this->assertEquals('removeTmp', $name);
            $this->assertInstanceOf(ProcessAwareInterface::class, $task);
            $this->assertEquals('/tmp', $task->getConfig()->path);
            $this->assertContains('srvcleaner*', $task->getConfig()->match);

            $task->setCurrentWorkingDirectory(dirname(__DIR__));

            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor($processor);
            }

            $task->execute();
        });
    }

    public function testRemoveDirectoriesWithModifiedTimeFilter()
    {
        $config = Config::fromFile(__DIR__ .'/config/config-clean-directories.json');
        $tasks = $config->getTasks();

        $processor1 = $this->getMock(Processor::class);
        $processor1->expects($this->any())->method('setCurrentWorkingDirectory')->with(
            $this->equalTo(dirname(__DIR__))
        );
        $processor1->expects($this->never())->method('execute');

        $filter = new \stdClass();
        $filter->path = '/tmp';
        $filter->match = ['srvcleaner*'];
        $filter->modifiedAt = 'P1D';

        $tasks->each(function (TaskInterface $task) use ($processor1, $filter) {
            $task->setConfig($filter);
            $task->setCurrentWorkingDirectory(dirname(__DIR__));

            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor($processor1);
            }

            $task->execute();
        });

        touch($this->tmpDir, mktime(1, 1, 1, 1, 1, 2007));
        clearstatcache();

        $processor2 = $this->getMock(Processor::class);
        $processor2->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$this->tmpDir}")
        );

        $tasks->each(function (TaskInterface $task) use ($processor2, $filter) {
            $task->setConfig($filter);
            $task->setCurrentWorkingDirectory(dirname(__DIR__));

            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor($processor2);
            }

            $task->execute();
        });
    }

    public function testRecursive()
    {
        $config = Config::fromFile(__DIR__ .'/config/config-clean-directories-recur.json');
        $tasks = $config->getTasks();

        $processor = $this->getMock(Processor::class);
        $processor->expects($this->once())->method('setCurrentWorkingDirectory')->with(
            $this->equalTo(dirname(__DIR__))
        );
        $processor->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$this->tmpDir}/recur")
        );

        mkdir ($this->tmpDir.'/recur');

        $tasks->each(function (TaskInterface $task, $name) use ($processor) {
            $this->assertEquals('removeTmp', $name);
            $this->assertInstanceOf(ProcessAwareInterface::class, $task);

            $task->setCurrentWorkingDirectory(dirname(__DIR__));

            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor($processor);
            }

            $task->execute();
        });

        rmdir($this->tmpDir.'/recur');
    }

    protected function tearDown()
    {
        rmdir($this->tmpDir);
    }
}
