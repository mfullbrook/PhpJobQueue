<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Worker;

use PhpJobQueue\PhpJobQueue;

/**
 * Manager Worker Class. Spawns Child Workers using pcntl
 */
class Manager extends AbstractWorker
{
    protected $numChildren;
     
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $children The number of child processes to spawn
     */
    public function __construct(PhpJobQueue $phpJobQueue, $numChildren)
    {
        parent::__construct($phpJobQueue, 'worker.manager');
        $this->numChildren = (int) $numChildren;
        
        $this->logger->debug('Manager PID = '.posix_getpid());
    }
    
    
    public function work()
    {
        if ($this->numChildren > 1) {
        	for ($i = 0; $i < $this->numChildren; ++$i) {
        		$pid = pcntl_fork();
        		if ($pid == -1) {
        		    $msg = sprintf('Could not fork worker %d of %d', $i+1, $this->numChildren);
        		    $this->logger->error($msg);
        		    die($msg);
        		}
        		elseif (!$pid) {
        		    // running as child
        		    $child = new Child($this->phpJobQueue);
        			break;
        		}
        	}
        }
        
    }
}
