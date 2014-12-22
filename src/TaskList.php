<?php
namespace Genkgo\Srvcleaner;

use ArrayIterator;
use Closure;
use Countable;
use IteratorAggregate;

/**
 * Class TaskList
 * @package Genkgo\Srvcleaner
 */
class TaskList implements Countable, IteratorAggregate
{
    /**
     * @var array
     */
    private $tasks = [];

    /**
     * @param $name
     * @param TaskInterface $task
     */
    public function add($name, TaskInterface $task)
    {
        $this->tasks[$name] = $task;
    }

    /**
     * @param callable $callback
     */
    public function each(Closure $callback)
    {
        foreach ($this->tasks as $name => $task) {
            $callback($task, $name);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->tasks);
    }

    /**
     * @return ArrayIterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->tasks);
    }
}
