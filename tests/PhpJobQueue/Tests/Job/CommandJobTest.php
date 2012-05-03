<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Job;

use PhpJobQueue\Tests\TestCase;
use PhpJobQueue\Job\CommandJob;
use PhpJobQueue\Tests\Mock\PhpJobQueue;

class CommandJobTest extends TestCase
{
    
    /**
     * @covers PhpJobQueue\Job\CommandJob::setCommand
     */
    public function testSetCommand()
    {
        $job = new CommandJob();
        $job->setCommand('test-command');
        $parameters = $job->getParameters();
        $this->assertEquals('test-command', $parameters['command']);
    }
    
    /**
     * @covers PhpJobQueue\Job\CommandJob::perform
     * @covers PhpJobQueue\Job\CommandJob::getLastLine
     * @covers PhpJobQueue\Job\CommandJob::getOutput
     * @covers PhpJobQueue\Job\CommandJob::getReturnCode
     */
    public function testPerform()
    {
        $pjq = new PhpJobQueue();
        
        $worker = $this->getMockForAbstractClass('PhpJobQueue\\Worker\\AbstractWorker', array($pjq, 'testlogger'));
        
        $job = new CommandJob();
        $job->setCommand('echo \'foobar\' && echo \'last-line\'');
        $job->perform($worker);
        
        $this->assertEquals('last-line', $job->getLastLine(), 'assert last line of output is correct');
        $this->assertEquals(array('foobar', 'last-line'), $job->getOutput(), 'assert output array is correct');
        $this->assertEquals(0, $job->getReturnCode(), 'assert return code is correct');
    }
}