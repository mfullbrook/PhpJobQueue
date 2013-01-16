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

/**
 * @Todo: Don't use a mock for Redis, mock the RedisConfig instead?
 */
class RedisTest extends TestCase
{
    public function testIdToKey()
    {
        $this->assertEquals(Redis::JOB_PREFIX.'id', Redis::idToKey('id'));
    }
    
    /**
     * @expectedException \PhpJobQueue\Exception\JobNotFoundException
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobNotFound()
    {
        $redis = $this->getRedisStorageMock(array('hgetall'));
        $redis->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue(null));
        
        $redis->getJob('id');
    }
    
    /**
     * @expectedException \PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 2
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithEmptyParamsThrowCorrupt()
    {
        $hash = array();
        
        $redis = $this->getRedisStorageMock(array('hgetall'));
        $redis->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $redis->getJob('id');
    }
    
    /**
     * @expectedException \PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 1
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithEmptyClassThrowCorrupt()
    {
        $hash = array('params' => 'NULL');
        
        $redis = $this->getRedisStorageMock(array('hgetall'));
        $redis->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $redis->getJob('id');
    }
    
    /**
     * @expectedException \PhpJobQueue\Exception\JobCorruptException
     * @expectedExceptionCode 3
     * @covers PhpJobQueue\Storage\Redis::getJob
     */
    public function testGetJobWithBadParamsThrowCorrupt()
    {
        $hash = array('class' => 'PhpJobQueue\\Job\\Job', 'params' => '[foo');
        
        $redis = $this->getRedisStorageMock(array('hgetall'));
        $redis->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $redis->getJob('id');
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
        
        $redis = $this->getRedisStorageMock(array('hgetall'));
        $redis->expects($this->once())
             ->method('hgetall')
             ->will($this->returnValue($hash));
        
        $returnedJob = $redis->getJob('id');
        
        $this->assertEquals($job, $returnedJob);
    }
    
    /**
     * @covers PhpJobQueue\Storage\Redis::jobStarted
     */
    public function testJobStarted()
    {
        $redis = $this->getRedisStorageMock(array('hset'));
        $redis->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $redis->jobStarted($job);
    }
    
    public function testJobCompleted()
    {
        $redis = $this->getRedisStorageMock(array('hset'));
        $redis->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $redis->jobCompleted($job);
    }
    
    public function testJobFailed()
    {
        $redis = $this->getRedisStorageMock(array('hset'));
        $redis->expects($this->exactly(2))
            ->method('hset');
        
        $job = new TestJob();
        $redis->jobFailed($job, 'test error');
    }

    public function testTraceWorkerStatus()
    {
        $worker = $this->getMockBuilder('PhpJobQueue\\Worker\\AbstractWorker')
            ->disableOriginalConstructor()
            ->getMock();
        
        $worker->expects($this->once())
            ->method('__toString')
            ->will($this->returnValue('foobar'));
        
        $redis = $this->getRedisStorageMock(array('sadd', 'hmset'));
        $redis->traceWorkerStatus($worker);
    }
}