<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests;

use PhpJobQueue\Tests\Mock\PhpJobQueue;
use PhpJobQueue\Config\Configuration;
use PhpJobQueue\Config\RedisConfig;


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
            $this->getMockBuilder('PhpJobQueue\\Queue\\Redis')
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
        date_default_timezone_set('UTC');
        $this->assertEquals(date('r'), PhpJobQueue::getUtcDateString());
    }
    
    /**
     * @covers PhpJobQueue\PhpJobQueue::offsetExists
     */
    public function testOffsetExists()
    {
        $pjq = new PhpJobQueue();
        $this->assertTrue(isset($pjq[$pjq->getDefaultQueueName()]));
        
        $this->assertFalse(isset($pjq['foo']));
    }
    
    /**
     * @covers PhpJobQueue\PhpJobQueue::offsetGet
     * @covers PhpJobQueue\PhpJobQueue::getQueue
     */
    public function testOffsetGet()
    {
        $pjq = new PhpJobQueue();
        $implements = class_implements($pjq[$pjq->getDefaultQueueName()]);
        $this->assertContains('PhpJobQueue\Queue\QueueInterface', $implements);
    }
    
    /**
     * @expectedException BadMethodCallException
     * @covers PhpJobQueue\PhpJobQueue::offsetSet
     */
    public function testOffsetSet()
    {
        $pjq = new PhpJobQueue();
        $pjq['a'] = 'b';
    }
    
    /**
     * @expectedException BadMethodCallException
     * @covers PhpJobQueue\PhpJobQueue::offsetUnset
     */
    public function testOffsetUnset()
    {
        $pjq = new PhpJobQueue();
        unset($pjq['a']);
    }
    
    
}