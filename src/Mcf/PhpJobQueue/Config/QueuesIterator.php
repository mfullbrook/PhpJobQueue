<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Config;

/**
 * Iterates a collection of queues in priority order
 */
class QueuesIterator implements \Iterator
{
    protected $queues;
    
    protected $iteratorPosition = 0;
    
    public function __construct(Array $queues)
    {
        $this->queues = $queues;
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
