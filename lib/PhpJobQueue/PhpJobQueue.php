<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue;

use PhpJobQueue\Config\ConfigurationInterface;
use PhpJobQueue\Config\Configuration;
use PhpJobQueue\Job\AbstractJob;

/**
 * Main class for PhpJobQueue
 */
class PhpJobQueue
{
    protected $config;
    
    protected $queues = array();
    
    protected $storage;
    
    /**
     * Class constructor
     */
    public function __construct($config=null)
    {
        if ($config instanceof ConfigurationInterface) {
            $this->config = $config;
        } else {
            $this->config = new Configuration($config);
        }
    }
    
    /**
     * @return PhpJobQueue\Config\Configuration
     */
    public function getConfig($key = null)
    {
        return is_null($key) ? $this->config : $this->config->$key;
    }
    
    /**
     * Adds a job to the specified queue
     */
    public function enqueue(AbstractJob $job, $queueName = 'default')
    {
        return $this->getQueue($queueName)->enqueue($job);
    }
    
    /**
     * Retrieves the next job from the queue(s)
     *
     * @return PhpJobQueue\Job\JobInterface
     */
    public function retrieve()
    {
        return $this->getQueue($queueName)->retrieve();
    }
    
    /**
     * Factory method for fetching a Queue instance
     * @todo allow hard coded redis queue to be switched
     */
    protected function getQueue($name)
    {
        if (!isset($this->queues[$name])) {
            $this->queues[$name] = new \PhpJobQueue\Queue\Redis($name, $this->getStorage());
        }
        return $this->queues[$name];
    }
    
    /**
     * Get an instance of a storage class
     * @todo allow hard coded redis storage to be switched
     */
    protected function getStorage()
    {
        if (!isset($this->storage)) {
            $this->storage = new \PhpJobQueue\Storage\Redis($this->config->redis);
        }
        return $this->storage;
    }
    
    public static function createUniqueId()
    {
        return sha1(uniqid());
    }
}


// create one or more jobs, add to one or more queues:
// enqueue:
// add jobs to a (redis) queue

// process jobs, jobs plucked from various queues
// getStorage:
// create Predis instance using the redis config
// retrieve:
// loop the queues names, get a queue instance which has been passed the storage instance
// fetches a job from a (redis) queue





