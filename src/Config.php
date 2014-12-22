<?php
namespace Genkgo\Srvcleaner;

use stdClass;
use Genkgo\Srvcleaner\Exceptions\ConfigurationException;

/**
 * Class Config
 * @package Genkgo\Srvcleaner
 */
class Config
{
    /**
     * @var stdClass
     */
    private $document;

    /**
     * @param stdClass $document
     */
    public function __construct(stdClass $document)
    {
        $this->setDocument($document);
    }

    /**
     * @param stdClass $document
     */
    public function setDocument(stdClass $document)
    {
        $this->document = $document;
    }

    /**
     * @return TaskList
     */
    public function getTasks()
    {
        $taskList = new TaskList;

        if (!isset($this->document->tasks) || !is_array($this->document->tasks)) {
            throw new ConfigurationException('No tasks found');
        }

        foreach ($this->document->tasks as $taskJson) {
            if (!isset($taskJson->name) || !isset($taskJson->src)) {
                throw new ConfigurationException('Task name and src are required');
            }

            $name = $taskJson->name;
            $className = $taskJson->src;
            $config = $taskJson->config;

            if (!$name || !$className) {
                throw new ConfigurationException('Task name and src cannot be empty');
            }

            if (strpos($className, '\\') === false) {
                $className = 'Genkgo\\Srvcleaner\\Tasks\\' . $className;
            }

            if (!class_exists($className)) {
                throw new ConfigurationException("Task {$name} not found. Unknown class {$className}");
            }

            $task = new $className ;
            if ($task instanceof TaskInterface) {
                $task->setConfig($config);
                $taskList->add($name, $task);
            } else {
                throw new ConfigurationException("Task is not implementing TaskInterface");
            }
        }

        return $taskList;
    }

    /**
     * @param $fileName
     * @return Config
     */
    public static function fromFile($fileName)
    {
        if (!file_exists($fileName)) {
            throw new ConfigurationException('Config file not found');
        }

        $source = file_get_contents($fileName);
        $json = json_decode($source);
        if ($json === null) {
            throw new ConfigurationException('Config is not valid json');
        }
        return new static ($json);
    }
}
