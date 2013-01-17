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
     * @covers \Mcf\PhpJobQueue\Job\Job::setId
     * @covers \Mcf\PhpJobQueue\Job\Job::getId
     * @covers \Mcf\PhpJobQueue\Job\Job::setParameters
     * @covers \Mcf\PhpJobQueue\Job\Job::getParameters
     * @covers \Mcf\PhpJobQueue\Job\Job::setStatus
     * @covers \Mcf\PhpJobQueue\Job\Job::getStatus
     * @covers \Mcf\PhpJobQueue\Job\Job::setQueueName
     * @covers \Mcf\PhpJobQueue\Job\Job::getQueueName
     * @covers \Mcf\PhpJobQueue\Job\Job::setQueuedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::getQueuedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::setStartedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::getStartedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::setCompletedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::getCompletedAt
     * @covers \Mcf\PhpJobQueue\Job\Job::setErrorDetails
     * @covers \Mcf\PhpJobQueue\Job\Job::getErrorDetails
     */
    public function testSetterAndGetters()
    {
        $job = $this->getMockForAbstractClass('Mcf\\PhpJobQueue\\Job\\Job');
        
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
     * @covers \Mcf\PhpJobQueue\Job\Job::__toString
     */
    public function testToString()
    {
        // sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($this))), $this->id);
        $job = $this->getMockForAbstractClass('Mcf\\PhpJobQueue\\Job\\Job');
        $job->setId('id');
        
        $string = sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($job))), 'id');
        $this->assertEquals($string, (string) $job);
    }
}