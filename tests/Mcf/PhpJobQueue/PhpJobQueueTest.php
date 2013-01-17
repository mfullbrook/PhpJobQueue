<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue;

use Mcf\PhpJobQueue\Mock\PhpJobQueue;
use Mcf\PhpJobQueue\Config\Configuration;
use Mcf\PhpJobQueue\Config\RedisConfig;


class PhpJobQueueTest extends TestCase
{
    protected $queueMock;
    protected $storageMock;
    
    public function testConstructor()
    {
        $config = new Configuration();
        $pjq = new PhpJobQueue($config);
        $this->assertEquals($config, $pjq->getConfig(), '->getConfig returns the Configuration instance passed in to constructor');
    }
    
    public function testGetConfigWithKey()
    {
        $pjq = new PhpJobQueue();
        $this->assertEquals(new RedisConfig(), $pjq->getConfig('redis'));
    }
    
    private function PhpJobQueueFactoryWithMocks()
    {
        $this->pjq = new PhpJobQueue();
        
        // create queue mock
        $this->queueMock =
            $this->getMockBuilder('Mcf\\PhpJobQueue\\Queue\\Redis')
                 ->disableOriginalConstructor()
                 ->getMock();
        $c = get_class($this->queueMock);
        //$m2 = new $c();
        
        //$this->assertEquals($c, $m2);
        
        $this->pjq->setClass('queue', ''); // mock
        $this->pjq->setClass('storage', ''); // mock
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetUnknownClassKey()
    {
        $pjq = new PhpJobQueue();
        $pjq->setClass('foo', 'bar');
    }
    
    public function testEnqueue()
    {
        $this->PhpJobQueueFactoryWithMocks();
    }
    
    public function testGetUtcDateString()
    {
        $d = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->assertEquals($d->format(\DateTime::ISO8601), PhpJobQueue::getUtcDateString());
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::offsetExists
     */
    public function testOffsetExists()
    {
        $pjq = new PhpJobQueue();
        $this->assertTrue(isset($pjq[$pjq->getDefaultQueueName()]));
        
        $this->assertFalse(isset($pjq['foo']));
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::offsetGet
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::getQueue
     */
    public function testOffsetGet()
    {
        $pjq = new PhpJobQueue();
        $this->assertInstanceOf('Mcf\PhpJobQueue\Queue\QueueInterface', $pjq[$pjq->getDefaultQueueName()]);
    }
    
    /**
     * @expectedException BadMethodCallException
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::offsetSet
     */
    public function testOffsetSet()
    {
        $pjq = new PhpJobQueue();
        $pjq['a'] = 'b';
    }
    
    /**
     * @expectedException BadMethodCallException
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::offsetUnset
     */
    public function testOffsetUnset()
    {
        $pjq = new PhpJobQueue();
        unset($pjq['a']);
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::attachLogHandlers
     */
    public function testAttachLogHandlers()
    {
        $logger = new \Monolog\Logger('test');
        $pjq = new \Mcf\PhpJobQueue\PhpJobQueue(array('general' => array('log' => array('enabled' => false))));
        $pjq->attachLogHandlers($logger);
        $this->assertInstanceOf('Monolog\\Handler\\NullHandler', $logger->popHandler());
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::getLogger
     */
    public function testGetLogger()
    {
        $pjq = new PhpJobQueue();
        $this->assertInstanceOf('Monolog\\Logger', $pjq->getLogger());
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::getClass
     */
    public function testGetClass()
    {
        $pjq = new PhpJobQueue();
        $this->assertEquals('Mcf\\PhpJobQueue\\Queue\\Redis', $pjq->getClass('queue'));
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::getStorage
     */
    public function testGetStorage()
    {
        $pjq = new PhpJobQueue();
        $this->assertInstanceOf($pjq->getClass('storage'), $pjq->getStorage());
    }
    
    /**
     * @covers \Mcf\PhpJobQueue\PhpJobQueue::findJob
     */
    public function testFindJob()
    {
        $storage = $this->getRedisStorageMock();
        $pjq = new PhpJobQueue();
        $pjq->setStorage($storage);
        
        
    }
}