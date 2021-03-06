<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Queue;

use Mcf\PhpJobQueue\Queue\Redis;
use Mcf\PhpJobQueue\Storage\Redis as RedisStorage;
use Mcf\PhpJobQueue\Config\RedisConfig;
use Mcf\PhpJobQueue\Job\JobInterface;
use Mcf\PhpJobQueue\Mock\TestJob;
use Mcf\PhpJobQueue\TestCase;
use Mcf\PhpJobQueue\Mock\PhpJobQueue;

class RedisTest extends TestCase
{
    /**
     * @covers \Mcf\PhpJobQueue\Queue\Redis::enqueue
     */
    public function testEnqueue()
    {
        date_default_timezone_set('UTC');
        
        $jobParams = array('param1' => 'value1');
        
        $job = new TestJob();
        $job->setParameters($jobParams);
        
        $phpJobQueue = new PhpJobQueue();
        
        $storage = $this->getMock('Mcf\\PhpJobQueue\\Storage\\Redis', array('hmset', 'rpush'), array(new RedisConfig()));

        $d = new \DateTime('now', new \DateTimeZone('UTC'));
        $storage
            ->expects($this->once())
            ->method('hmset')
            ->with($this->equalTo(RedisStorage::idToKey('JOBIDX')),
                   $this->equalTo(array(
                        'class' => 'Mcf\\PhpJobQueue\\Mock\\TestJob',
                        'params' => json_encode($jobParams),
                        'status' => JobInterface::STATUS_WAITING,
                        'queue' => Redis::QUEUE_PREFIX . 'testQueue',
                        'queuedAt' => $d->format(\DateTime::ISO8601)
                   )))
            ->id('hmset');
        
        $storage
            ->expects($this->once())
            ->method('rpush')
            ->after('hmset');
        
        $redisQueue = $this->getMockBuilder('Mcf\\PhpJobQueue\\Queue\\Redis')
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
     * @covers \Mcf\PhpJobQueue\Queue\Redis::retrieve
     */
    public function testRetrieve()
    {
        $phpJobQueue = new PhpJobQueue();
        
        $storage = $this->getMock('Mcf\\PhpJobQueue\\Storage\\Redis', array('lpop', 'getJob'), array(new RedisConfig()));
        
        $queueName = 'testqueue';
        
        $storage
            ->expects($this->any())
            ->method('lpop')
            ->with($this->equalTo(Redis::QUEUE_PREFIX . $queueName))
            ->will($this->onConsecutiveCalls('newJobId', 'foo', 'bar'));
        
        $testJob = new TestJob();
        $testJob->setId('newJobId');
        
        $storage
            ->expects($this->any())
            ->method('getJob')
            ->will($this->returnCallback(function($id) use($testJob) {
                if ($id == 'newJobId')
                    return $testJob;
                else if ($id == 'foo')
                    throw new \Mcf\PhpJobQueue\Exception\JobNotFoundException();
                else if ($id == 'bar')
                    throw new \Mcf\PhpJobQueue\Exception\JobCorruptException();
            } ));
        
        $redisQueue = new Redis($queueName, $phpJobQueue, $storage);
        $retrievedJob = $redisQueue->retrieve();
        
        $this->assertEquals($testJob, $retrievedJob);
        
        // test the exceptions
        $redisQueue->retrieve();
        $this->assertContains(array(
            'message' => 'Job foo in queue queue:testqueue was not found',
            'level' => 300,
            'level_name' => 'WARNING',
            'channel' => 'queue.redis'
        ), $phpJobQueue->getBufferedLogs(\Monolog\Logger::WARNING));
        
        $redisQueue->retrieve();
        $this->assertContains(array(
            'message' => 'Job bar in queue queue:testqueue is corrupt',
            'level' => 300,
            'level_name' => 'WARNING',
            'channel' => 'queue.redis'
        ), $phpJobQueue->getBufferedLogs(\Monolog\Logger::WARNING));
        
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\Queue\Redis::countJobs
     */
    public function testCountJobs()
    {
        $phpJobQueue = new PhpJobQueue();
        
        $storage = $this->getMock('Mcf\\PhpJobQueue\\Storage\\Redis', array('llen'), array(new RedisConfig()));
        
        $queueName = 'testqueue';
        
        $storage
            ->expects($this->once())
            ->method('llen')
            ->with($this->equalTo(Redis::QUEUE_PREFIX . $queueName))
            ->will($this->returnValue(5));
            
        $redisQueue = new Redis($queueName, $phpJobQueue, $storage);
        
        $this->assertEquals(5, $redisQueue->countJobs());
    }
    
    public function testCreateJobId()
    {
        $pjq = $this->getMock('Mcf\\PhpJobQueue\\PhpJobQueue');
        $storage = $this->getMock('Mcf\\PhpJobQueue\\Storage\\Redis', array(), array(), '', false);
        
        $queue = new Redis('test', $pjq, $storage);
        $this->assertInternalType('string', $queue->createJobId());
    }
    
    public function testToString()
    {
        $pjq = $this->getMock('Mcf\\PhpJobQueue\\PhpJobQueue');
        $storage = $this->getMock('Mcf\\PhpJobQueue\\Storage\\Redis', array(), array(), '', false);
        
        $queue = new Redis('test', $pjq, $storage);
        $this->assertEquals('{RedisQueue:queue:test}', (string) $queue);
    }
}
