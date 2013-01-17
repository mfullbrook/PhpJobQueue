<?php

namespace Mcf\PhpJobQueue\Job;

use Mcf\PhpJobQueue\Worker\AbstractWorker;

interface JobInterface
{
    const STATUS_WAITING = 'WAITING';
    const STATUS_WORKING = 'WORKING';
    const STATUS_COMPLETE = 'COMPLETE';
    const STATUS_FAILED = 'FAILED';

    /**
     * Validate that the job has the correct parameters to perform the job
     */
    public function validate();

    /**
     * @param \Mcf\PhpJobQueue\Worker\AbstractWorker $worker
     */
    public function perform(AbstractWorker $worker);

    /**
     * Return the JSON serializable data
     * @return mixed
     */
    public function jsonSerialize();
}