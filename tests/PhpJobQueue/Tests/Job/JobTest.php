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
    
    public function testToString()
    {
        // sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($this))), $this->id);
        $job = $this->getMockForAbstractClass('PhpJobQueue\Job\Job');
        $job->setId('id');
        
        $string = sprintf('{%s:%s}', basename(str_replace('\\', '/', get_class($job))), 'id');
        $this->assertEquals($string, (string) $job);
    }
}