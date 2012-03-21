<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Queue;

use PhpJobQueue\Job\Job;
use PhpJobQueue\PhpJobQueue;
use PhpJobQueue\Storage\Redis as RedisStorage;
use PhpJobQueue\Exception\JobNotFoundException;
use PhpJobQueue\Exception\JobCorruptException;
use Monolog\Logger;

/**
 * Redis implementation of the Queue
 */
class Redis implements QueueInterface
{
    const QUEUE_PREFIX = 'queue:';

    protected $name;
    
    /**
     * @var PhpJobQueue\PhpJobQueue
     */
    protected $phpJobQueue;
    
    /**
     * @var PhpJobQueue\Storage\Redis
     */
    protected $storage;
    
    /**
     * @var Monolog\Logger
     */
    protected $logger;
    
    /**
     * Construct the Queue with a name and StorageInstance
     * @param string $name
     * @param PhpJobQueue\PhpJobQueue $phpJobQueue
     * @param PhpJobQueue\Storage\Redis
     */
    public function __construct($name, PhpJobQueue $phpJobQueue, RedisStorage $storage)
    {
        $this->name = self::QUEUE_PREFIX . $name;
        $this->phpJobQueue = $phpJobQueue;
        $this->storage = $storage;
        $this->logger = $this->phpJobQueue->attachLogHandlers(new Logger('queue.redis'));
    }
    
    /**
     * {@inheritDoc}
     */
    public function enqueue(Job $job)
    {
        $id = PhpJobQueue::createUniqueId();
        
        // store the job in a hash
        $this->storage->hmset(self::idToKey($id), array(
            'class' => get_class($job),
            'params' => json_encode($job->getParameters()),
            'status' => Job::STATUS_WAITING,
            'queue' => $this->name,
            'queuedAt' => PhpJobQueue::getUtcDateString(),
        ));
        
        // add the id to the queue
        $this->storage->rpush($this->name, $id);
        
        return $id;
    }
    
    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        // use lpop to find a job ID
        $jobId = $this->storage->lpop($this->name);
        
        if ($jobId) {
            $this->logger->info(sprintf('Job ID %s was retrieved from %s', $jobId, $this));
            
            // fetch and return the job
            try {
                return $this->storage->getJob($jobId);
            } catch (JobNotFoundException $e) {
                $this->logger->warn(sprintf('Job %s in queue %s was not found', $jobId, $this->name));
            } catch (JobCorruptException $e) {
                $this->logger->warn(sprintf('Job %s in queue %s is corrupt', $jobId, $this->name));
            }
        }
        
        return null;
    }
        
    /**
     * {@inheritDoc}
     */
    public function countJobs()
    {
        return $this->storage->llen($this->name);
    }
    
    /** 
     * Convert the queue to a string representation
     */
    public function __toString()
    {
        return sprintf('{RedisQueue:%s}', $this->name);
    }
}
