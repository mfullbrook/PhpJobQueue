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

use PhpJobQueue\Job\Job;

/**
 * Interface that defines the methods a storage class must expose
 */
interface StorageInterface
{
    /**
     * Returns a storage key from the id.
     *
     * @param string $id
     * @return string
     */
    public static function idToKey($id);
    
    /**
     * Get a job object from an ID
     *
     * @throws PhpJobQueue\Exception\JobNotFoundException
     * @throws PhpJobQueue\Exception\JobCorruptException
     *
     * @return PhpJobQueue\Job
     */
    public function getJob($id);
    
    /**
     * Notifies the database that a job has been started.
     * The database should record the time this occurred.
     * 
     * @param PhpJobQueue\Job\Job $job
     */
    public function jobStarted(Job $job);
    
    /**
     * Notifies the database that a job completed successfully.
     * The database should record the time this occurred.
     * 
     * @param PhpJobQueue\Job\Job $job
     */
    public function jobCompleted(Job $job);
    
    /**
     * Notifies the database that a job failed with an exception or error message
     * 
     * @param PhpJobQueue\Job\Job $job
     * @param mixed $error Error message or typically an exception
     */
    public function jobFailed(Job $job, $error);    
}