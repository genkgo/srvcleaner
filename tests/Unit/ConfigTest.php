<?php
namespace Genkgo\Srvcleaner\Unit;

use Genkgo\Srvcleaner\AbstractTestCase;
use Genkgo\Srvcleaner\Config;
use Genkgo\Srvcleaner\Exceptions\ConfigurationException;
use Genkgo\Srvcleaner\TaskInterface;

class ConfigTest extends AbstractTestCase
{
    public function testGetTasks()
    {
        $config = Config::fromFile(__DIR__ .'/config/config-test.json');
        $tasks = $config->getTasks();

        $this->assertCount(3, $tasks);

        $this->assertContainsOnlyInstancesOf(TaskInterface::class, $tasks);
    }

    public function testNoTasks()
    {
        $this->setExpectedException(ConfigurationException::class);

        $config = Config::fromFile(__DIR__ .'/config/config-without-tasks.json');
        $config->getTasks();
    }

    public function testInvalidJson()
    {
        $this->setExpectedException(ConfigurationException::class);

        Config::fromFile(__DIR__ .'/config/config-invalid.json');
    }

    public function testFileNotExists()
    {
        $this->setExpectedException(ConfigurationException::class);

        Config::fromFile(__DIR__ .'/config/file-not-exists.json');
    }

    public function testInvalidTask()
    {
        $this->setExpectedException(ConfigurationException::class);

        $config = Config::fromFile(__DIR__ .'/config/config-task-invalid.json');
        $config->getTasks();
    }

    public function testTaskNameAndSrcCannotBeEmpty()
    {
        $this->setExpectedException(ConfigurationException::class);

        $config = Config::fromFile(__DIR__ .'/config/config-task-empty.json');
        $config->getTasks();
    }
}
