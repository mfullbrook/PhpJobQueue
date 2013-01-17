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

class JobTest extends TestCase
{
    /**
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setId
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getId
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setParameters
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getParameters
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setStatus
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getStatus
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setQueueName
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getQueueName
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setQueuedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getQueuedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setStartedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getStartedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setCompletedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getCompletedAt
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::setErrorDetails
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::getErrorDetails
     */
    public function testSetterAndGetters()
    {
        $job = $this->getMockForAbstractClass('Mcf\\PhpJobQueue\\Job\\AbstractJob');
        
        $job->setId('123');
        $this->assertEquals('123', $job->getId());
        
        $job->setParameters(array('foo' => 'bar'));
        $this->assertEquals(array('foo' => 'bar'), $job->getParameters());
        
        $job->setStatus(1);
        $this->assertEquals(1, $job->getStatus());
        
        $job->setQueueName('foo');
        $this->assertEquals('foo', $job->getQueueName());
        
        $time = date('r');
        
        $job->setQueuedAt($time);
        $this->assertEquals($time, $job->getQueuedAt());

        $job->setStartedAt($time);
        $this->assertEquals($time, $job->getStartedAt());
        
        $job->setCompletedAt($time);
        $this->assertEquals($time, $job->getCompletedAt());
        
        $job->setErrorDetails(array('error' => 'details'));
        $this->assertEquals(array('error' => 'details'), $job->getErrorDetails());    
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\Job\AbstractJob::__toString
     */
    public function testToString()
    {
        // sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($this))), $this->id);
        $job = $this->getMockForAbstractClass('Mcf\\PhpJobQueue\\Job\\AbstractJob');
        $job->setId('id');
        
        $string = sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($job))), 'id');
        $this->assertEquals($string, (string) $job);
    }
}