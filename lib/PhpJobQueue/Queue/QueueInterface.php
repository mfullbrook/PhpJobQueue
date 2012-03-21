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

use PhpJobQueue\Job\Job;

/**
 * Interface that describes an individual job queue.
 */
interface QueueInterface
{
    /**
     * Add a Job to the queue
     * @param PhpJobQueue\Job\Job
     * @return string The ID of the enqueued job
     */
    public function enqueue(Job $job);
    
    /**
     * @return PhpJobQueue\Job\Job Returns a job or null if there is nothing in the queue 
     */
    public function retrieve();
    
    /**
     * Count the number of jobs in the queue
     */
    public function countJobs();
    
}