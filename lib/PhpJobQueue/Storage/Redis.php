<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Storage;

use Predis\Client;
use PhpJobQueue\PhpJobQueue;
use PhpJobQueue\Config\RedisConfig;
use PhpJobQueue\Job\Job;

/**
 * Extend Predis Client so that we can manipulate the config
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

         $params = json_decode($hash['params'], true);

         if (empty($hash['class'])) {
             throw new JobCorruptException('The class field was empty');
         }

         if ($params === null) {
             throw new JobCorruptException('The params field did not decode successfully');
         }

         $job = new $hash['class']();
         $job->setId($id);
         $job->setParameters($params);
         $job->setStatus($hash['status']);
         $job->setQueue($hash['queue']);
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
        $this->hset(self::idToKey($id), 'status', Job::STATUS_WORKING);
        $this->hset(self::idToKey($id), 'startedAt', PhpJobQueue::getUtcDateString());
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobCompleted(Job $job)
    {
        $this->hset(self::idToKey($id), 'status', Job::STATUS_COMPLETE);
        $this->hset(self::idToKey($id), 'completedAt', PhpJobQueue::getUtcDateString());
    }
    
    /**
     * {@inheritDoc}
     */
    public function jobFailed(Job $job, $error)
    {
        $this->hset(self::idToKey($id), 'status', Job::STATUS_FAILED);
        $this->hset(self::idToKey($id), 'errorDetails', (string) $error);
    }
    
}