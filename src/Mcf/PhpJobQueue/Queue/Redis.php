<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Queue;

use Mcf\PhpJobQueue\Job\JobInterface;
use Mcf\PhpJobQueue\PhpJobQueue;
use Mcf\PhpJobQueue\Storage\Redis as RedisStorage;
use Mcf\PhpJobQueue\Exception\JobNotFoundException;
use Mcf\PhpJobQueue\Exception\JobCorruptException;
use Monolog\Logger;

/**
 * Redis implementation of the Queue
 */
class Redis implements QueueInterface
{
    const QUEUE_PREFIX = 'queue:';

    protected $name;
    
    /**
     * @var PhpJobQueue
     */
    protected $phpJobQueue;
    
    /**
     * @var RedisStorage
     */
    protected $storage;
    
    /**
     * @var Logger
     */
    protected $logger;
    
    /**
     * Construct the Queue with a name and StorageInstance
     * @param string $name
     * @param PhpJobQueue $phpJobQueue
     * @param RedisStorage
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
    public function enqueue(JobInterface $job)
    {
        $job->validate();
        
        $id = $this->createJobId();
        $job->setId($id);
        
        // store the job in a hash
        $this->storage->hmset(RedisStorage::idToKey($id), array(
            'class' => get_class($job),
            'params' => json_encode($job->getParameters()),
            'status' => JobInterface::STATUS_WAITING,
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
    
    public function createJobId()
    {
        return sha1(uniqid());
    }
}
