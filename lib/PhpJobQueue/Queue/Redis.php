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

use PhpJobQueue\Storage\Redis as RedisStorage;
use PhpJobQueue\Job\AbstractJob;
use PhpJobQueue\PhpJobQueue;

/**
 * Redis implementation of the Queue
 */
class Redis implements QueueInterface
{
    const QUEUE_PREFIX = 'queue:';
    
    protected $name;
    
    protected $storage;
    
    /**
     * Construct the Queue with a name and StorageInstance
     * @param string $name
     * @param RedisStorage $storage
     */
    public function __construct($name, RedisStorage $storage)
    {
        $this->name = self::QUEUE_PREFIX . $name;
        $this->storage = $storage;
    }
    
    /**
     * {@inheritDoc}
     */
    public function enqueue(AbstractJob $job)
    {
        $id = PhpJobQueue::createUniqueId();
        
        $details = array(
            'class' => get_class($job),
            'params' => $job->getParameters(),    
        );
        
        // store the job
        $this->storage->set("job:$id", json_encode($details));
        
        // add the id to the queue
        $this->storage->rpush($this->name, $id);
        
        return $id;
    }
    
    /**
     * {@inheritDoc}
     */
    public function retrieve()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function countJobs()
    {
        return $this->storage->llen($this->name);
    }
    
    /**
     * {@inheritDoc}
     */
    public function getJobStatus()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function setJobStatus()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobCompleted()
    {
        
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobFailed()
    {
        
    }
    
}
