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
use Monolog\Logger;

/**
 * Abstract Worker class that provides shared functionality
 */
abstract class AbstractWorker
{
    const STATUS_WAITING = 0;
    const STATUS_WORKING = 1;
    const STATUS_DESPATCHED = 2;
    
    /**
     * @var PhpJobQueue\PhpJobQueue
     */
    protected $phpJobQueue;
    
    /**
     * @var PhpJobQueue\Storage\StorageInterface
     */
    protected $storage;
    
    /**
     * @var Monolog\Logger
     */
    protected $logger;
    
    /**
     * An array of queues names to retrieve jobs from, if empty will retrieve from any queue
     */
    protected $queuesFilter = array();
    
    /**
     * Timestamp of when this worker began working
     */
    protected $started;
    
    /**
     * The current status of the worker
     */
    protected $status;
    
    /**
     * An integer counter of the jobs worked
     */
    protected $worked;
    
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $children The number of child processes to spawn
     */
    public function __construct(PhpJobQueue $phpJobQueue, StorageInterface $storage, $loggerName)
    {
        $this->phpJobQueue = $phpJobQueue;
        $this->storage = $storage;
        $this->logger = $this->phpJobQueue->attachLogHandlers(new Logger($loggerName));
        $this->status = self::STATUS_WAITING;
        $this->worked = 0;
    }
    
    
    /**
     * By default the worker will attempt to work against all the queues, use setQueues to set a 
     * subset of queues to work.
     */
    public function setQueuesFilter(Array $queues)
    {
        $this->queuesFilter = $queues;
    }
    
    public function getQueuesFilter()
    {
        return $this->queuesFilter;
    }
    
    /**
	 * On supported systems (with the PECL proctitle module installed), update
	 * the name of the currently running process to indicate the current state
	 * of a worker.
	 *
	 * @param string $status The updated process title.
	 */
    public static function updateProcLine($status)
	{
		if (function_exists('setproctitle')) {
			setproctitle('PhpJobQueue: ' . $status);
		}
	}
	
	/**
	 * Return the logger instance
	 *
	 * @return Monolog\Logger
	 */
	public function getLogger()
	{
	    return $this->logger;
	}
	
	/**
	 * Record the start time and notify the storage
	 */
	public function started()
	{
	    $this->started = PhpJobQueue::getUtcDateString();
	    $this->storage->traceWorkerStatus($this);
	}
	
	/**
	 * Update the status and notify the storage
	 */
	public function status($newStatus)
	{
	    $this->status = $newStatus;
	    $this->storage->traceWorkerStatus($this);
	}
	
	/**
	 * Increment the worked count and notify the storage
	 */
	public function worked()
	{
	    $this->worked++;
	    $this->storage->traceWorkerStatus($this);
	}

    public function getStarted()
    {
        return $this->started;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getWorked()
    {
        return $this->worked;
    }

	/**
	 * Creates and returns a trace info object with the values injected
	 *
	 * @return PhpJobQueue\Worker\TraceInfo
	 */
	public function getTraceInfo()
	{
	    $type = basename(str_replace('\\', '/', get_class($this)));;
	    return new TraceInfo(getmypid(), $type, $this->started, $this->status, $this->worked);
	}
	
	/**
	 * Return a name for this worker
	 */
	public function __toString()
	{
	    $type = basename(str_replace('\\', '/', get_class($this)));
        return sprintf('Worker%s:%s:%s', $type, gethostname(), getmypid());
	}
	
	/**
	 * Called to start this worker working
	 */
	abstract public function work();
}
