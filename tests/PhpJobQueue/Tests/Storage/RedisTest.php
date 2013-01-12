<?php 

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Storage\Redis;

use PhpJobQueue\Tests\TestCase;
use PhpJobQueue\Storage\Redis;
use PhpJobQueue\Tests\Mock\TestJob;

class RedisTest extends TestCase
{
    public function testIdToKey()
    {
        $this->assertEquals(Redis::JOB_PREFIX.'id', Redis::idToKey('id'));
    }
    
    /**
     * @expectedException PhpJobQueue\Exception\JobNotFoundException
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobNotFound()
    {
        $mock = $this->getRedisStorageMock(array('hgetall'));
        $mock->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue(null));
        
        $mock->getJob('id');
    }
    
    /**
     * @expectedException PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 2
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithEmptyParamsThrowCorrupt()
    {
        $hash = array();
        
        $mock = $this->getRedisStorageMock(array('hgetall'));
        $mock->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $mock->getJob('id');
    }
    
    /**
     * @expectedException PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 1
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithEmptyClassThrowCorrupt()
    {
        $hash = array('params' => 'NULL');
        
        $mock = $this->getRedisStorageMock(array('hgetall'));
        $mock->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $mock->getJob('id');
    }
    
    /**
     * @expectedException PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 3
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithBadParamsThrowCorrupt()
    {
        $hash = array('class' => 'PhpJobQueue\\Job\\Job', 'params' => '[foo');
        
        $mock = $this->getRedisStorageMock(array('hgetall'));
        $mock->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $mock->getJob('id');
    }
    
    /**
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJob()
    {
        $hash = array(
            'class' => 'PhpJobQueue\\Tests\\Mock\\TestJob',
            'params' => '{"foo":"bar"}',
            'id' => 'id',
            'status' => \PhpJobQueue\Job\Job::STATUS_FAILED,
            'queue' => 'testQueue',
            'queuedAt' => date('r', time() - 300),
            'startedAt' => date('r', time() - 10),
            'completedAt' => date('r'),
            'errorDetails' => 'errorDetails is currently just a string'
        );
        
        $job = new \PhpJobQueue\Tests\Mock\TestJob();
        $job->setId('id');
        $job->setParameters(array('foo' => 'bar'));
        $job->setStatus(\PhpJobQueue\Job\Job::STATUS_FAILED);
        $job->setQueueName('testQueue');
        $job->setQueuedAt(date('r', time() - 300));
        $job->setStartedAt(date('r', time() - 10));
        $job->setCompletedAt(date('r'));
        $job->setErrorDetails('errorDetails is currently just a string');
        
        $mock = $this->getRedisStorageMock(array('hgetall'));
        $mock->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $returnedJob = $mock->getJob('id');
        
        $this->assertEquals($job, $returnedJob);
    }
    
    /**
     * @covers PhpJobQueue\Storage\Redis::jobStarted
     */
    public function testJobStarted()
    {
        $mock = $this->getRedisStorageMock(array('hset'));
        $mock->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $mock->jobStarted($job);
    }
    
    public function testJobCompleted()
    {
        $mock = $this->getRedisStorageMock(array('hset'));
        $mock->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $mock->jobCompleted($job);
    }
    
    public function testJobFailed()
    {
        $mock = $this->getRedisStorageMock(array('hset'));
        $mock->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $mock->jobFailed($job, 'test error');
    }
}