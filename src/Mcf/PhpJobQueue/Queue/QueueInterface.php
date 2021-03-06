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

/**
 * Interface that describes an individual job queue.
 */
interface QueueInterface
{
    /**
     * Add a Job to the queue
     * @param JobInterface
     * @return string The ID of the enqueued job
     */
    public function enqueue(JobInterface $job);
    
    /**
     * @return JobInterface Returns a job or null if there is nothing in the queue
     */
    public function retrieve();
    
    /**
     * Count the number of jobs in the queue
     */
    public function countJobs();
    
    /**
     * @return string an identifier to be used for a Job
     */
    public function createJobId();
}