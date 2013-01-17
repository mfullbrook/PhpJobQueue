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

use Mcf\PhpJobQueue\Job\JobInterface;
use Mcf\PhpJobQueue\Worker\AbstractWorker;
use Mcf\PhpJobQueue\Worker\TraceInfo;

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
     * @throws \Mcf\PhpJobQueue\Exception\JobNotFoundException
     * @throws \Mcf\PhpJobQueue\Exception\JobCorruptException
     *
     * @return Job
     */
    public function getJob($id);
    
    /**
     * Notifies the database that a job has been started.
     * The database should record the time this occurred.
     * 
     * @param Job $job
     */
    public function jobStarted(JobInterface $job);
    
    /**
     * Notifies the database that a job completed successfully.
     * The database should record the time this occurred.
     * 
     * @param Job $job
     */
    public function jobCompleted(JobInterface $job);
    
    /**
     * Notifies the database that a job failed with an exception or error message
     * 
     * @param Job $job
     * @param mixed $error Error message or typically an exception
     */
    public function jobFailed(JobInterface $job, $error);
    
    /**
     * Find a job by job ID
     *
     * @param string
     * @return Job $job
     */
    public function findJob($id);
    
    # workers: pid, status, current job
    
    /**
     * Updates the database with the status, pid and current job (if working),
     * time started and worked count.
     * 
     * @param AbstractWorker $worker
     */
    public function traceWorkerStatus(AbstractWorker $worker);
    
    /**
     * Remove from the database the tracking information for a worker
     *
     * @param AbstractWorker $worker
     */
    public function workerTerminated(AbstractWorker $worker);
    
    /**
     * Gets all the trace information of the workers
     *
     * @return TraceInfo[]
     */
    public function getWorkersTraceInfo();
}