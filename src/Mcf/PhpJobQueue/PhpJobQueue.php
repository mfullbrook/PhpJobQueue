<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue;

use Mcf\PhpJobQueue\Config\ConfigurationInterface;
use Mcf\PhpJobQueue\Config\Configuration;
use Mcf\PhpJobQueue\Exception\QueueNotFoundException;
use Mcf\PhpJobQueue\Job\JobInterface;
use Monolog\Logger;
use DateTime, DateTimeZone;

/**
 * Main class for PhpJobQueue
 * Implements ArrayAccess
 */
class PhpJobQueue implements \ArrayAccess
{
    const DEFAULT_QUEUE = 'default';
    
    /**
     * @var \Mcf\PhpJobQueue\Config\ConfigurationInterface
     */
    protected $config;
    
    /**
     * @var array of PhpJobQueue\Queue\QueueInterface
     */
    protected $queues = array();
    
    /**
     * @var \Mcf\PhpJobQueue\Storage\Redis
     */
    protected $storage;
    
    /**
     * The logger for the PhpJobQueue class
     *
     * @var \Monolog\Logger
     */
    protected $logger;
    
    protected $classes = array(
        'queue'   => 'Mcf\\PhpJobQueue\\Queue\\Redis',
        'storage' => 'Mcf\\PhpJobQueue\\Storage\\Redis',
        'worker'  => 'Mcf\\PhpJobQueue\\Worker\\Manager'
    );
    
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
        
        // create the logger
        $this->logger = new Logger('core');
        $this->attachLogHandlers($this->logger);
    }
    
    /**
     * @return \Mcf\PhpJobQueue\Config\Configuration
     */
    public function getConfig($key = null)
    {
        return is_null($key) ? $this->config : $this->config->$key;
    }
    
    /**
     * @return \Monolog\Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Set the class for a given identifier
     */
    public function setClass($key, $class)
    {
        if (array_key_exists($key, $this->classes)) {
            $this->classes[$key] = $class;
        } else {
            throw new \InvalidArgumentException('The class key you have requested to override is unknown.');
        }
    }
    
    public function getClass($key)
    {
        return $this->classes[$key];
    }
    
    /**
     * Adds a job to the specified queue
     *
     * @param Job $job The job to add to the end of the queue
     * @param string $queueName The name of the queue, if not supplied uses default queue
     */
    public function enqueue(JobInterface $job, $queueName = null)
    {
        if (is_null($queueName)) {
            $queueName = static::DEFAULT_QUEUE;
        }
        
        $jobId = $this->getQueue($queueName)->enqueue($job);
        
        $this->logger->info(sprintf('New job added to queue \'%s\' with job ID: %s', $queueName, $jobId));
        
        return $jobId;
    }
    
    /**
     * @param int $children The number of child processes to spawn
     */
    public function work($numWorkers)
    {
        /** @var $worker \Mcf\PhpJobQueue\Worker\AbstractWorker */
        $class = $this->getClass('worker');
        $worker = new $class($this, $this->getStorage(), $numWorkers);
        $worker->work();
    }
    
    /**
     * Find a job by job ID
     *
     * @param string
     * @return \Mcf\PhpJobQueue\Job\JobInterface $job
     */
    public function findJob($id)
    {
        return $this->getStorage()->findJob($id);
    }
    
    /**
     * Gets all the trace information of the workers
     *
     * @return \Mcf\PhpJobQueue\Worker\TraceInfo[]
     */
     public function getWorkersTraceInfo()
     {
         return $this->getStorage()->getWorkersTraceInfo();
     }
    
    /**
     * Factory method for fetching a Queue instance
     *
     * @throws \Mcf\PhpJobQueue\Exception\QueueNotFoundException
     */
    protected function getQueue($name)
    {
        // check that the queue exists, throws an exception if not
        $this->config->queues->hasQueue($name, true);
        
        if (!isset($this->queues[$name])) {
            $class = $this->getClass('queue');
            $this->queues[$name] = new $class($name, $this, $this->getStorage());
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
            $class = $this->getClass('storage');
            $this->storage = new $class($this->config->redis);
            $this->logger->debug('Storage (Predis) initialised');
        }
        return $this->storage;
    }
    
    /**
     * ArrayAccess method: abstract public boolean offsetExists ( mixed $offset )
     */
    public function offsetExists($offset)
    {
        try {
            $this->getQueue($offset);
            return true;
        } catch (QueueNotFoundException $e) {
            return false;
        }
    }
    
    /**
     * ArrayAccess method: abstract public mixed offsetGet ( mixed $offset )
     */
    public function offsetGet($offset)
    {
        return $this->getQueue($offset);
    }
    
    /**
     * ArrayAccess method: abstract public void offsetSet ( mixed $offset , mixed $value )
     */
    public function offsetSet($offset, $value)
    {
        throw new \BadMethodCallException('Cannot set queues using ArrayAccess');
    }
    
    /**
     * ArrayAccess method: abstract public void offsetUnset ( mixed $offset )
     */
    public function offsetUnset($offset)
    {
        throw new \BadMethodCallException('Cannot unset queues using ArrayAccess');
    }
    
    /**
     * Convenience method to GeneralConfig::attachLogHandlers
     *
     * @param \Monolog\Logger $logger
     */ 
    public function attachLogHandlers(Logger $logger)
    {
        $this->config->general->attachLogHandlers($logger);
        return $logger;
    }

    /**
     * Static method using late static binding to retrieve the default queue name
     * @return string
     */
    public static function getDefaultQueueName()
    {
        return static::DEFAULT_QUEUE;
    }
    
    
    public static function getUtcDateString()
    {
        $d = new DateTime('now', new DateTimeZone('UTC'));
        return $d->format(DateTime::ISO8601);
    }
}
