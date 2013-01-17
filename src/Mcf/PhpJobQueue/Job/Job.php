<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Job;

use Mcf\PhpJobQueue\Worker\AbstractWorker;

abstract class Job
{
    const STATUS_WAITING = 'WAITING';
    const STATUS_WORKING = 'WORKING';
    const STATUS_COMPLETE = 'COMPLETE';
    const STATUS_FAILED = 'FAILED';
    
    protected $id;
    
    protected $parameters = array();
    
    protected $status;
    
    protected $queueName;
    
    protected $queuedAt;
    
    protected $startedAt;
    
    protected $completedAt;
    
    protected $errorDetails;
    
    
    public function setId($id)
    {
        $this->id = $id;
    }
    
    public function getId()
    {
        return $this->id;
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Status Getter
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Status Setter
     * @param string
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }
    
    /**
     * QueueName Getter
     * @return string
     */
    public function getQueueName()
    {
        return $this->queueName;
    }

    /**
     * QueueName Setter
     * @param string
     */
    public function setQueueName($queueName)
    {
        $this->queueName = $queueName;
    }
    
    /**
     * QueuedAt Getter
     * @return string
     */
    public function getQueuedAt()
    {
        return $this->queuedAt;
    }

    /**
     * QueuedAt Setter
     * @param string
     */
    public function setQueuedAt($queuedAt)
    {
        $this->queuedAt = $queuedAt;
    }

    /**
     * StartedAt Getter
     * @return string
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * StartedAt Setter
     * @param string
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * CompletedAt Getter
     * @return string
     */
    public function getCompletedAt()
    {
        return $this->completedAt;
    }

    /**
     * CompletedAt Setter
     * @param string
     */
    public function setCompletedAt($completedAt)
    {
        $this->completedAt = $completedAt;
    }

    /**
     * ErrorDetails Getter
     * @return string
     */
    public function getErrorDetails()
    {
        return $this->errorDetails;
    }

    /**
     * ErrorDetails Setter
     * @param string
     */
    public function setErrorDetails($errorDetails)
    {
        $this->errorDetails = $errorDetails;
    }

    
    
    public function __toString()
    {
        return sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($this))), $this->id);
    }
    
    abstract public function validate();
    
    abstract public function perform(AbstractWorker $worker);
}