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

use Mcf\PhpJobQueue\TestCase;
use Mcf\PhpJobQueue\Job\CommandJob;
use Mcf\PhpJobQueue\Mock\PhpJobQueue;

class CommandJobTest extends TestCase
{
    
    /**
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::setCommand
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::validate
     */
    public function testSetCommand()
    {
        $job = new CommandJob();
        $job->setCommand('test-command');
        $this->assertEquals(array('command' => 'test-command'), $job->getParameters());
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::validate
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::perform
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::getLastLine
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::getOutput
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::getReturnCode
     */
    public function testPerform()
    {
        $pjq = new PhpJobQueue();
        $storage = $this->getRedisStorageMock();
        
        $worker = $this->getMockForAbstractClass('Mcf\\PhpJobQueue\\Worker\\AbstractWorker', array($pjq, $storage, 'testlogger'));
        
        $job = new CommandJob();
        $job->setCommand('echo \'foobar\' && echo \'last-line\'');
        $job->perform($worker);
        
        $this->assertEquals('last-line', $job->getLastLine(), 'assert last line of output is correct');
        $this->assertEquals(array('foobar', 'last-line'), $job->getOutput(), 'assert output array is correct');
        $this->assertEquals(0, $job->getReturnCode(), 'assert return code is correct');
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\Job\CommandJob::validate
     * @expectedException \Mcf\PhpJobQueue\Exception\JobInvalidException
     */
    public function testValidate()
    {
        $job = new CommandJob();
        $job->validate();
    }
}