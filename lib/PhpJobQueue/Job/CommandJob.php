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

use PhpJobQueue\Worker\AbstractWorker;

class CommandJob extends Job
{
    protected $command;
    protected $output;
    protected $lastLine;
    protected $returnCode;
    
    public function setCommand($command)
    {
        $this->parameters['command'] = $command;
    }
    
    public function perform(AbstractWorker $worker)
    {
        unset($this->lastLine, $this->output, $this->returnCode);
        $this->lastLine = exec($this->parameters['command'], $this->output, $this->returnCode);
        $worker->getLogger()->info(sprintf('Last line from %s = %s', $this, $this->lastLine));
    }
    
    public function getLastLine()
    {
        return $this->lastLine;
    }
    
    public function getOutput()
    {
        return $this->output;
    }
    
    public function getReturnCode()
    {
        return $this->returnCode;
    }
}