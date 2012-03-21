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
use PhpJobQueue\Job\Job;

/**
 * Child Worker. Processes a job one at a time
 */
class Child extends AbstractWorker
{
    /**
	 * @var boolean True if on the next iteration, the worker should shutdown.
	 */
	protected $shutdown = false;

	/**
	 * @var boolean True if this worker is paused.
	 */
	protected $paused = false;
	
    protected $interval;
    
    protected $context;
    
    protected $workerPID;
    
    /**
     * @var PhpJobQueue\Config\QueuesIterator
     */
    protected $iterator;
    
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $processId The process ID assigned to this worker
     */
    public function __construct(PhpJobQueue $phpJobQueue, $forked=false)
    {
        parent::__construct($phpJobQueue, 'worker.child');
        
        $this->interval = $this->phpJobQueue->getConfig('general')->workerInterval;
        
        $this->context = array(
            'pid' => getmypid(),
            'ppid' => $forked ? posix_getppid() : null,
            'host' => gethostname()
        );
        
        if ($forked) {
            $this->logger->info('Forked child worker', $this->context);
        }
    }
        
    public function work()
    {
        $this->registerSigHandlers();
        
        while (true) {
            // break if shutdown has been scheduled
            if ($this->shutdown) {
                break;
            }
            
            // unless we are paused attempted to retrieve a job
            $job = false;
            if (!$this->paused) {
                $job = $this->retrieveJob();
            }
            
            if (!$job) {
                // break if interval is zero
                if ($this->interval == 0) {
                    break;
                }
                
                $this->logger->debug('Waiting for a job', $this->context);
                
                // sleep then begin loop again
                sleep($this->interval);
                continue;
            }
            
            $this->logger->info('Retrieved job: '.$job, $this->context);
            
            // todo: update statuses etc
            
            
            $this->workerPID = $this->fork();

			// Forked and we're the child. Run the job.
			if ($this->workerPID === 0 || $this->workerPID === false) {
				// todo some logging
				$this->perform($job);
				// if we are a child process then now exit
				if ($this->workerPID === 0) {
					exit(0);
				}
			}

			if ($this->workerPID > 0) {
				// Parent process, sit and wait
				// todo: some logging

				// Wait until the child process finishes before continuing
				pcntl_wait($status);
				$exitStatus = pcntl_wexitstatus($status);
				if($exitStatus !== 0) {
				    $this->logger->error('Job exited with exit code: '.$exitStatus, $this->context);
				}
			}

			$this->child = null;
        }
        
    }
    
    /**
	 * Attempt to fork a child that will perform the job.
	 *
	 * Return values are those of pcntl_fork().
	 *
	 * @return int -1 if the fork failed, 0 for the forked child, the PID of the child for the parent.
	 */
	protected function fork()
	{
		if(!function_exists('pcntl_fork')) {
			return false;
		}

		$pid = pcntl_fork();
		
		if ($pid === -1) {
		    $this->logger->critical('Unable to fork process to perform job', $this->context);
			throw new \RuntimeException('Unable to fork process to perform job');
		} elseif ($pid) {
		    $this->logger->info('Forked process to perform job', $this->context);
        }
        
		return $pid;
	}
    
    /**
     * Attempt to retrieve a job from one of the queues
     *
     * @return PhpJobQueue\Job\Job Returns a job or false if there is nothing in the queue 
     */
    protected function retrieveJob()
    {
        if (!isset($this->iterator)) {
            $this->iterator = $this->phpJobQueue->getConfig('queues')->getFilteredIterator($this->queuesFilter);
        }
        
        foreach ($this->iterator as $queue) {
            $job = $this->phpJobQueue[$queue]->retrieve();
            if ($job !== null) {
                break;
            }
        }
        
        return $job;
    }
    
    /**
     * Actually perform the job
     *
     * @param PhpJobQueue\Job\Job $job The job to perform
     */
    protected function perform(Job $job)
    {
        try {
			$job->perform($this);
		}
		catch(Exception $e) {
			$this->logger->error($job . ' threw an exception: ' . $e->getMessage(), $this->context);
			//$job->fail($e);
			return;
		}

        // todo: update status and logging.
    }
    
    /**
	 * Register signal handlers that a worker should respond to.
	 *
	 * TERM: Shutdown immediately and stop processing jobs.
	 * INT: Shutdown immediately and stop processing jobs.
	 * QUIT: Shutdown after the current job finishes processing.
	 * USR1: Kill the forked child immediately and continue processing jobs.
	 */
	protected function registerSigHandlers()
	{
		if(!function_exists('pcntl_signal')) {
			return;
		}
        return;
        /**
		declare(ticks = 1);
		pcntl_signal(SIGTERM, array($this, 'shutDownNow'));
		pcntl_signal(SIGINT, array($this, 'shutDownNow'));
		pcntl_signal(SIGQUIT, array($this, 'shutdown'));
		pcntl_signal(SIGUSR1, array($this, 'killChild'));
		pcntl_signal(SIGUSR2, array($this, 'pauseProcessing'));
		pcntl_signal(SIGCONT, array($this, 'unPauseProcessing'));
		pcntl_signal(SIGPIPE, array($this, 'reestablishRedisConnection'));
		$this->log('Registered signals', self::LOG_VERBOSE);
		*/
	}
}
