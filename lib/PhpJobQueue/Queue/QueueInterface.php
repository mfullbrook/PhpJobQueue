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

use PhpJobQueue\Job\AbstractJob;

/**
 * Interface that describes an individual job queue.
 */
interface QueueInterface
{
    public function enqueue(AbstractJob $job);
    
    /**
     * @return PhpJobQueue\Job\JobInterface
     */
    public function retrieve();
    
    public function getJobStatus();
    
    public function setJobStatus();
    
    public function jobCompleted();
    
    public function jobFailed();
    
}