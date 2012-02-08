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

use PhpJobQueue\Worker\WorkerInterface;

class CommandJob extends AbstractJob
{
    protected $command;
    
    public function setCommand($command)
    {
        $this->parameters['command'] = $command;
    }
    
    public function execute(WorkerInterface $worker)
    {
        passthru($this->parameters['command']);
    }
}