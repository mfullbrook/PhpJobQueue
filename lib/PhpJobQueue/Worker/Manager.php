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
use PhpJobQueue\Storage\StorageInterface;

/**
 * Manager Worker Class. Spawns Child Workers using pcntl
 */
class Manager extends AbstractWorker
{
    protected $numWorkers;
     
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $numWorkers The number of child processes to spawn
     */
    public function __construct(PhpJobQueue $phpJobQueue, StorageInterface $storage, $numWorkers)
    {
        parent::__construct($phpJobQueue, $storage, 'worker.manager');
        $this->numWorkers = (int) $numWorkers;
        
        $this->logger->debug('Manager PID = '.posix_getpid());
    }
    
    /**
     * Fork a new process for each child worker if the number of required workers is greater than one.
     */
    public function work()
    {
        if ($this->numWorkers > 1) {
        	for ($i = 0; $i < $this->numWorkers; ++$i) {
        		$pid = pcntl_fork();
        		if ($pid == -1) {
        		    $msg = sprintf('Could not fork worker %d of %d', $i+1, $this->numWorkers);
        		    $this->logger->error($msg);
        		    die($msg);
        		}
        		elseif (!$pid) {
        		    // running as forked process
        		    $this->logger->info('Forked worker process '.getmypid());
        		    fwrite(STDOUT, '*** Starting worker with PID '.getmypid()."\n");
        		    $child = $this->childFactory(true);
        		    $child->work();
        			break;
        		}
        	}
        }
        else {
        	$this->logger->info('Single process worker, starting child.');
        	$this->started();
        	$this->status = AbstractWorker::STATUS_DESPATCHED;
        	$child = $this->childFactory(false);
		    $child->work();
        }
        
        
    }
    
    /**
     * Creates a new child and injects the dependencies
     *
     * @return PhpJobQueue\Worker\Child
     */
    public function childFactory($forked)
    {
        $child = new Child($this->phpJobQueue, $forked);
        $child->setQueuesFilter($this->queuesFilter);
        return $child;
    }
}
