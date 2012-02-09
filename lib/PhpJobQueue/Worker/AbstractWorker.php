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
use Monolog\Logger;

/**
 * Abstract Worker class that provides shared functionality
 */
class AbstractWorker
{
    protected $phpJobQueue;
    
    protected $logger;

    protected $queues;
    
    
    
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $children The number of child processes to spawn
     */
    public function __construct(PhpJobQueue $phpJobQueue, $loggerName)
    {
        $this->phpJobQueue = $phpJobQueue;
        $this->logger = $this->phpJobQueue->attachLogHandlers(new Logger($loggerName));
    }
    
    
    /**
     * By default the worker will attempt to work against all the queues, use setQueues to set a 
     * subset of queues to work.
     */
    public function setQueues(Array $queues)
    {
        $this->queues = $queues;
    }
    
}
