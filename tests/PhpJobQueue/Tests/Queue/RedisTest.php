<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Queue;

use PhpJobQueue\Queue\Redis;
use PhpJobQueue\Storage\Redis as RedisStorage;
use PhpJobQueue\Config\RedisConfig;
use PhpJobQueue\Job\Job;
use PhpJobQueue\Tests\Mock\TestJob;
use PhpJobQueue\Tests\TestCase;
use PhpJobQueue\Tests\Mock\PhpJobQueue;

class RedisTest extends TestCase
{
    /**
     * @covers PhpJobQueue\Queue\Redis::enqueue
     */
    public function testEnqueue()
    {
        date_default_timezone_set('UTC');
        
        $jobParams = array('param1' => 'value1');
        
        $job = new TestJob();
        $job->setParameters($jobParams);
        
        $phpJobQueue = new PhpJobQueue();
        
        $storage = $this->getMock('PhpJobQueue\\Storage\\Redis', array('hmset', 'rpush'), array(new RedisConfig()));

        $storage
            ->expects($this->once())
            ->method('hmset')
            ->with($this->equalTo(RedisStorage::idToKey('JOBIDX')),
                   $this->equalTo(array(
                        'class' => 'PhpJobQueue\\Tests\\Mock\\TestJob',
                        'params' => json_encode($jobParams),
                        'status' => Job::STATUS_WAITING,
                        'queue' => Redis::QUEUE_PREFIX . 'testQueue',
                        'queuedAt' => date('r')
                   )))
            ->id('hmset');
        
        $storage
            ->expects($this->once())
            ->method('rpush')
            ->after('hmset');
        
        $redisQueue = $this->getMockBuilder('PhpJobQueue\\Queue\\Redis')
            ->setMethods(array('createJobId'))
            ->setConstructorArgs(array('testQueue', $phpJobQueue, $storage))
            ->getMock();
        
        $redisQueue
            ->expects($this->once())
            ->method('createJobId')
            ->will($this->returnValue('JOBIDX'));

        $id = $redisQueue->enqueue($job);
        
        $this->assertEquals('JOBIDX', $id);
    }
    
    /**
     * @covers PhpJobQueue\Queue\Redis::retrieve
     */
    public function testRetrieve()
    {
        $logger = $this->loggerFactory();
        
        $phpJobQueue = new PhpJobQueue();
        
        $storage = $this->getMock('PhpJobQueue\\Storage\\Redis', array('lpop', 'getJob'), array(new RedisConfig()));
        
        $queueName = 'testqueue';
        
        $storage
            ->expects($this->once())
            ->method('lpop')
            ->with($this->equalTo(Redis::QUEUE_PREFIX . $queueName))
            ->will($this->returnValue('newJobId'));
        
        $testJob = new TestJob();
        $testJob->setId('newJobId');
        
        $this->markTestIncomplete();
        
        $storage
            ->expects($this->once())
            ->method('getJob')
            ->with($this->equalTo('newJobId'))
            ->will($this->returnValue($testJob));
        
        $redisQueue = new Redis($queueName, $phpJobQueue, $storage);
        $retrievedJob = $redisQueue->retrieve();
        
        $this->assertEquals($testJob, $retrievedJob);
        
        $storage
            ->expects($this->once())
            ->method('getJob')
            ->with($this->equalTo('foo'));
//            ->will($this->throwException(new \PhpJobQueue\Exception\JobNotFoundException));
        
        $storage->getJob('foo');
    }
    
    public function testCreateJobId()
    {
        $pjq = $this->getMock('PhpJobQueue\\PhpJobQueue');
        $storage = $this->getMock('PhpJobQueue\\Storage\\Redis', array(), array(), '', false);
        
        $queue = new Redis('test', $pjq, $storage);
        $this->assertInternalType('string', $queue->createJobId());
    }
    
    public function testToString()
    {
        $pjq = $this->getMock('PhpJobQueue\\PhpJobQueue');
        $storage = $this->getMock('PhpJobQueue\\Storage\\Redis', array(), array(), '', false);
        
        $queue = new Redis('test', $pjq, $storage);
        $this->assertEquals('{RedisQueue:queue:test}', (string) $queue);
    }
}
