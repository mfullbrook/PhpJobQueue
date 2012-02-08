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

/**
 * Define the queues, priorities and any other queue related configuration.
 * Implements Iterator in order of priority (from highest to lowest)
 * Implements ArrayAccess to access a queue configuration by name
 */
class QueuesConfig implements \Iterator
{
    protected $queues;
    
    protected $priorities;
    
    protected $iteratorPosition;
    
    protected $configuration;
    
    /**
     * Set the defaults
     */
    public function __construct()
    {
        $this->queues = array('queue');
        $this->priorities = array(0);
        $this->iteratorPosition = 0;
    }
    
    /**
     * Take the configuration of queues and ensure they are in numerical order
     */
    public function initialise($input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException('The `redis` configuration section is invalid.');
        }
        
        $queues = array();
        foreach ($input as $q) {
            $queues[ $q['priority'] ] = $q['name'];
        }
        
        if (count($queues)) {
            ksort($queues);
            $this->queues = array_values($queues);
            $this->priorities = array_keys($queues);
        }
    }
    
    /**
     * Returns the position bad on the queue name
     */
    protected function getPosition($name)
    {
        if (false === ($position = array_search($name, $this->queues))) {
            throw new QueueNotFoundException();
        }
        return $position;
    }
    
    /**
     * Iterator method: public mixed current ( void )
     */
    public function current()
    {
        return $this->queues[ $this->iteratorPosition ];
    }
    
    /**
     * Iterator method: abstract public scalar key ( void )
     */
    public function key()
    {
        return $this->iteratorPosition;
    }
    
    /**
     * Iterator method: abstract public void next ( void )
     */
    public function next()
    {
        $this->iteratorPosition++;
    }
    
    /**
     * Iterator method: abstract public void rewind ( void )
     */
    public function rewind()
    {
        $this->iteratorPosition = 0;
    }
    
    /**
     * Iterator method: abstract public boolean valid ( void )
     */
    public function valid()
    {
        return isset($this->queues[$this->iteratorPosition]);
    }
    
    /**
     * ArrayAccess method: abstract public boolean offsetExists ( mixed $offset )
     */
    public function offsetExists($offset)
    {
        return isset($this->queues[$this->getPosition($offset)]);
    }
    /**
     * abstract public mixed offsetGet ( mixed $offset )
     */
    public function offsetGet($offset)
    {
        return $this->queues[$this->getPosition($offset)];        
    }
    
    /**
     * abstract public void offsetSet ( mixed $offset , mixed $value )
     */
    public function offsetSet($offset, $value)
    {
        // do nothing
    }
    
    /**
     * abstract public void offsetUnset ( mixed $offset )
     */
    public function offsetUnset($offset)
    {
        // do nothing
    }
}
