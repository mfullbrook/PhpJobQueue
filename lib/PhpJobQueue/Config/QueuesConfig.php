<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Config;

use PhpJobQueue\PhpJobQueue;
use PhpJobQueue\Exception\QueueNotFoundException;

/**
 * Define the queues, priorities and any other queue related configuration.
 * Implements Iterator in order of priority (from highest to lowest)
 * Implements ArrayAccess to access a queue configuration by name
 */
class QueuesConfig implements \IteratorAggregate
{
    protected $queues;
    
    protected $priorities;
    
    protected $configuration;
    
    /**
     * Set the defaults
     */
    public function __construct()
    {
        $this->queues = array(PhpJobQueue::getDefaultQueueName());
        $this->priorities = array(0);
    }
    
    /**
     * Take the configuration of queues and ensure they are in numerical order
     */
    public function initialise($input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException('The `queues` configuration section is invalid.');
        }
        
        $queues = array();
        foreach ($input as $q) {
            $queues[ $q['priority'] ] = $q['name'];
        }
        
        // ensure the default queue exists
        if (false === array_search(PhpJobQueue::getDefaultQueueName(), $queues)) {
            $queues[0] = PhpJobQueue::getDefaultQueueName();
        }
        
        // sort by priority order
        if (count($queues)) {
            ksort($queues);
            $this->queues = array_values($queues);
            $this->priorities = array_keys($queues);
        }
    }
    
    /**
     * Returns the position of the queue name
     */
    protected function getPosition($name)
    {
        if (false === ($position = array_search($name, $this->queues))) {
            throw new QueueNotFoundException();
        }
        return $position;
    }
    
    /**
     * Does the queue with given name exist
     * @param string $name
     */
    public function hasQueue($name, $throw=false)
    {
        try {
            $this->getPosition($name);
        } catch (QueueNotFoundException $e) {
            if ($throw) {
                throw $e;
            }
            return false;
        }
        return true;
    }
    
    /**
     * IteratorAggregate method
     *
     * @return PhpJobQueue\Config\QueuesIterator
     */
    public function getIterator()
    {
        return new QueuesIterator($this->queues);
    }
    
    /**
     * Returns an iterator which contains a subset of queues
     *
     * @return PhpJobQueue\Config\QueuesIterator
     */
    public function getFilteredIterator($filter = array())
    {
        if (!is_array($filter) || !count($filter)) {
            throw new \InvalidArgumentException('getFilteredIterator expects an array of queue names');
        } 
        
        return new QueuesIterator(array_values(array_intersect($this->queues, $filter)));
    }
    
    /**
     * ArrayAccess method: abstract public boolean offsetExists ( mixed $offset )
     *
    public function offsetExists($offset)
    {
        return isset($this->queues[$this->getPosition($offset)]);
    }
    /**
     * ArrayAccess method: abstract public mixed offsetGet ( mixed $offset )
     *
    public function offsetGet($offset)
    {
        return $this->queues[$this->getPosition($offset)];        
    }
    
    /**
     * ArrayAccess method: abstract public void offsetSet ( mixed $offset , mixed $value )
     *
    public function offsetSet($offset, $value)
    {
        // do nothing
    }
    
    /**
     * ArrayAccess method: abstract public void offsetUnset ( mixed $offset )
     *
    public function offsetUnset($offset)
    {
        // do nothing
    }
    */
}
