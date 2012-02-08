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
    protected $name;
    
    protected $storage;
    
    public function __construct($name, RedisStorage $storage)
    {
        $this->name = $name;
        $this->storage = $storage;
    }

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
    
    public function retrieve()
    {
        
    }
    
    public function getJobStatus()
    {
        
    }
    
    public function setJobStatus()
    {
        
    }
    
    public function jobCompleted()
    {
        
    }
    
    public function jobFailed()
    {
        
    }
    
}
