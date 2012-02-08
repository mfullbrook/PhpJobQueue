<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Job;

abstract class AbstractJob
{
    protected $parameters = array();
    
    public function __construct($parameters = null)
    {
        if (!is_null($parameters)) {
            $this->setParameters($parameters);
        }
    }
    
    public function getParameters()
    {
        return $this->parameters;
    }
    
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
    
    abstract public function execute(\PhpJobQueue\Worker\WorkerInterface $worker);
}