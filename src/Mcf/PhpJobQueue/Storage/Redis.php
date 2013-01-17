<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Storage;

use Predis\Client;
use Mcf\PhpJobQueue\PhpJobQueue;
use Mcf\PhpJobQueue\Config\RedisConfig;
use Mcf\PhpJobQueue\Job\Job;
use Mcf\PhpJobQueue\Exception\JobCorruptException;
use Mcf\PhpJobQueue\Exception\JobNotFoundException;
use Mcf\PhpJobQueue\Worker\AbstractWorker;
use Mcf\PhpJobQueue\Worker\TraceInfo;

/**
 * Extend Predis Client so that we can manipulate the config
 *
 * @Todo: Don't extend Redis, inject it.
 */
class Redis extends Client implements StorageInterface
{
    const JOB_PREFIX = 'job:';
    
    public function __construct(RedisConfig $config)
    {
        parent::__construct($config->getParameters(), $config->getOptions());
    }
    
    /**
     *  Converts an ID to a Redis Key. The default implementation is to prefix the key with a class constant.
     * 
     * @param string $job
     * @return string
     */
    public static function idToKey($id)
    {
        return self::JOB_PREFIX . $id;
    }
    
    /**
     * {@inheritDoc}
     */
     public function getJob($id)
     {
         $hash = $this->hgetall(self::idToKey($id));
         if ($hash === null) {
             throw new JobNotFoundException();
         }
         
         if (empty($hash['params'])) {
             throw new JobCorruptException('The params field was empty', JobCorruptException::EMPTY_PARAMS);
         }

         $params = json_decode($hash['params'], true);

         if (empty($hash['class'])) {
             throw new JobCorruptException('The class field was empty', JobCorruptException::EMPTY_CLASS);
         }

         if ($params === null) {
             throw new JobCorruptException('The params field did not decode successfully', JobCorruptException::JSON_DECODE_FAILED);
         }

         /* @var $job Job */
         $job = new $hash['class']();
         $job->setId($id);
         $job->setParameters($params);
         $job->setStatus($hash['status']);
         $job->setQueueName($hash['queue']);
         $job->setQueuedAt($hash['queuedAt']);
         if (isset($hash['startedAt'])) {
             $job->setStartedAt($hash['startedAt']);
         }
         if (isset($hash['completedAt'])) {
             $job->setCompletedAt($hash['completedAt']);
         }
         if (isset($hash['errorDetails'])) {
             $job->setErrorDetails($hash['errorDetails']);
         }

         return $job;
     }
    
    /**
     * {@inheritDoc}
     */
    public function jobStarted(Job $job)
    {
        $job->setStatus(Job::STATUS_WORKING);
        $job->setStartedAt(PhpJobQueue::getUtcDateString());
        $this->hset(self::idToKey($job->getId()), 'status', $job->getStatus());
        $this->hset(self::idToKey($job->getId()), 'startedAt', $job->getStartedAt());
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobCompleted(Job $job)
    {
        $job->setStatus(Job::STATUS_COMPLETE);
        $job->setCompletedAt(PhpJobQueue::getUtcDateString());
        $this->hset(self::idToKey($job->getId()), 'status', $job->getStatus());
        $this->hset(self::idToKey($job->getId()), 'completedAt', $job->getCompletedAt());
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobFailed(Job $job, $error)
    {
        $job->setStatus(Job::STATUS_FAILED);
        $job->setErrorDetails(PhpJobQueue::getUtcDateString() . "\n" . (string)$error);
        $this->hset(self::idToKey($job->getId()), 'status', $job->getStatus());
        $this->hset(self::idToKey($job->getId()), 'errorDetails', $job->getErrorDetails());
    }
    
    /**
     * Find a job by job ID
     *
     * @param string
     * @return \PhpJobQueue\Job\Job $job
     */
    public function findJob($id)
    {
        // Todo: implement this method...
    }
    
    /**
     * Updates the database with the status, pid and current job (if working),
     * time started and worked count.
     * 
     * @param AbstractWorker $worker
     */
    public function traceWorkerStatus(AbstractWorker $worker)
    {
        $key = (string) $worker;
        $this->sadd('workers', $key);
        $this->hmset($key, $worker->getTraceInfo()->toArray());
    }
    
    /**
     * Remove from the database the tracking information for a worker
     *
     * @param AbstractWorker $worker
     */
    public function workerTerminated(AbstractWorker $worker)
    {
        $key = (string) $worker;
        $this->srem('workers', $key);
        $this->rem($key);
    }
    
    /**
     * Gets all the trace information of the workers
     *
     * @return \PhpJobQueue\Worker\TraceInfo[]
     */
    public function getWorkersTraceInfo()
    {
        $workers = array();
        foreach ($this->smembers('workers') as $key) {
            if ($this->exists($key)) {
                $workers[] = TraceInfo::fromArray($this->hgetall($key));
            }
        }
    }
    
}