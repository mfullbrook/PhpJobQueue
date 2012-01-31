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
 * Define the queues, priorities and any other related configuration.
 */
class QueuesConfig implements \Iterator
{
    protected $queues;
    
    protected $priorities;
    
    protected $iteratorPosition;
    
    /**
     * Set the defaults
     */
    public function __construct()
    {
        $this->queues = array(PhpJobQueue\PhpJobQueue::DEFAULT_QUEUE);
        $this->priorities = array(0);
        $this->iteratorPosition = 0;
    }
    
    /**
     * Take the configuration of queues and ensure they are in numerical order
     */
    public function processInput($input)
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
    
}
