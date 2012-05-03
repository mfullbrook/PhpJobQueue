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

class JobTest extends TestCase
{
    /**
     * @covers PhpJobQueue\Job\Job::setId
     * @covers PhpJobQueue\Job\Job::getId
     * @covers PhpJobQueue\Job\Job::setParameters
     * @covers PhpJobQueue\Job\Job::getParameters
     * @covers PhpJobQueue\Job\Job::setStatus
     * @covers PhpJobQueue\Job\Job::getStatus
     * @covers PhpJobQueue\Job\Job::setQueueName
     * @covers PhpJobQueue\Job\Job::getQueueName
     * @covers PhpJobQueue\Job\Job::setQueuedAt
     * @covers PhpJobQueue\Job\Job::getQueuedAt
     * @covers PhpJobQueue\Job\Job::setStartedAt
     * @covers PhpJobQueue\Job\Job::getStartedAt
     * @covers PhpJobQueue\Job\Job::setCompletedAt
     * @covers PhpJobQueue\Job\Job::getCompletedAt
     * @covers PhpJobQueue\Job\Job::setErrorDetails
     * @covers PhpJobQueue\Job\Job::getErrorDetails
     */
    public function testSetterAndGetters()
    {
        $job = $this->getMockForAbstractClass('PhpJobQueue\Job\Job');
        
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
     * @covers PhpJobQueue\Job\Job::__toString
     */
    public function testToString()
    {
        // sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($this))), $this->id);
        $job = $this->getMockForAbstractClass('PhpJobQueue\Job\Job');
        $job->setId('id');
        
        $string = sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($job))), 'id');
        $this->assertEquals($string, (string) $job);
    }
}