<?php
namespace Genkgo\Srvcleaner\Integration;

use Genkgo\Srvcleaner\AbstractTestCase;
use Genkgo\Srvcleaner\Config;
use Genkgo\Srvcleaner\TaskInterface;
use Genkgo\Srvcleaner\Util\ProcessAwareInterface;
use Genkgo\Srvcleaner\Util\Processor;

class CleanFilesTest extends AbstractTestCase
{
    private $tmpFile;

    protected function setUp()
    {
        $this->tmpFile = sys_get_temp_dir().'/srvcleaner'.uniqid();
        touch($this->tmpFile);
    }


    public function testRemoveDirectories()
    {
        $config = Config::fromFile(__DIR__ .'/config/config-clean-files.json');
        $tasks = $config->getTasks();

        $this->assertCount(1, $tasks);
        $this->assertContainsOnlyInstancesOf(TaskInterface::class, $tasks);

        $processor = $this->getMock(Processor::class);
        $processor->expects($this->once())->method('setCurrentWorkingDirectory')->with(
            $this->equalTo(dirname(__DIR__))
        );
        $processor->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$this->tmpFile}")
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
        $config = Config::fromFile(__DIR__ .'/config/config-clean-files.json');
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

        touch($this->tmpFile, mktime(1, 1, 1, 1, 1, 2007));
        clearstatcache();

        $processor2 = $this->getMock(Processor::class);
        $processor2->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$this->tmpFile}")
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
        $config = Config::fromFile(__DIR__ .'/config/config-clean-files-recur.json');
        $tasks = $config->getTasks();

        $tmpDir = dirname($this->tmpFile);

        $processor = $this->getMock(Processor::class);
        $processor->expects($this->once())->method('setCurrentWorkingDirectory')->with(
            $this->equalTo(dirname(__DIR__))
        );
        $processor->expects($this->once())->method('execute')->with(
            $this->equalTo("rm -Rf {$tmpDir}/recur/recur.srvclean")
        );

        mkdir ($tmpDir.'/recur');
        touch ($tmpDir.'/recur/recur.srvclean');

        $tasks->each(function (TaskInterface $task, $name) use ($processor) {
            $this->assertEquals('removeTmp', $name);
            $this->assertInstanceOf(ProcessAwareInterface::class, $task);

            $task->setCurrentWorkingDirectory(dirname(__DIR__));

            if ($task instanceof ProcessAwareInterface) {
                $task->setProcessor($processor);
            }

            $task->execute();
        });

        unlink($tmpDir.'/recur/recur.srvclean');
        rmdir ($tmpDir.'/recur');
    }

    protected function tearDown()
    {
        unlink($this->tmpFile);
    }
}
