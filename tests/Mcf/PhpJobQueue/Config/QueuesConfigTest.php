<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Config;

use Mcf\PhpJobQueue\Config\QueuesConfig;
use Mcf\PhpJobQueue\PhpJobQueue;

class PublicQueuesConfig extends QueuesConfig
{
    public function getPosition($v)
    {
        return parent::getPosition($v);
    }
}

class QueuesConfigTest extends \PHPUnit_Framework_Testcase
{
    protected $queueData = array(
        array('name' => 'high', 'priority' => 10),
        array('name' => 'low', 'priority' => -5),
    );
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testThrowsInvalidArgumentException()
    {
        $queues = new QueuesConfig();
        $queues->initialise('not an array');
    }
    
    /**
     * Ensure that the defaults are correct
     */
    public function testConstructor()
    {
        $name = PhpJobQueue::getDefaultQueueName();
        $queues = new PublicQueuesConfig();
        $this->assertTrue($queues->hasQueue($name));
        $this->assertEquals(0, $queues->getPosition($name));
    }
    
    public function testMissingQueueNameReturnsFalse()
    {
        $queues = new QueuesConfig();
        $this->assertFalse($queues->hasQueue('missing'));
    }
    
    /**
     * @expectedException \Mcf\PhpJobQueue\Exception\QueueNotFoundException
     */
    public function testMissingQueueNameThrowsException()
    {
        $queues = new QueuesConfig();
        $queues->hasQueue('missing', true);
    }
    
    public function testInitialise()
    {
        $queues = new PublicQueuesConfig();
        $queues->initialise($this->queueData);
        
        // ensure that the default queue has been added
        $defaultName = PhpJobQueue::getDefaultQueueName();
        $this->assertEquals(1, $queues->getPosition($defaultName), 'the default queue is added into the correct position');
        $this->assertTrue($queues->hasQueue($defaultName));
        
        $result = array();
        foreach ($queues as $q) {
            $result[] = $q;
        }
        
        $this->assertEquals(array('low', 'default', 'high'), $result, 'the returned iterator has the correct queues');
    }
    
    public function testFilteredIterator()
    {
        $queues = new QueuesConfig();
        $queues->initialise($this->queueData);
        
        $result = array();
        foreach ($queues->getFilteredIterator(array('low', 'high')) as $q) {
            $result[] = $q;
        }
        
        $this->assertEquals(array('low', 'high'), $result, '->getFilteredIterator returns only the queues supplied');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFilteredIteratorThrowsInvalidArgumentException()
    {
        $queues = new QueuesConfig();
        $queues->getFilteredIterator('not an array');
    }
    
    /**
     * This function should probably be in a new test class
     */
    public function testQueuesIterator()
    {
        $defaultName = PhpJobQueue::getDefaultQueueName();
        $queues = new QueuesConfig();
        $iterator = $queues->getIterator();
        $this->assertEquals($defaultName, $iterator->current());
        $this->assertEquals(0, $iterator->key());
    }
}