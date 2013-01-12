<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Worker;

use PhpJobQueue\Tests\TestCase;
use PhpJobQueue\Worker\AbstractWorker as AbstractWorkerOriginal;
use PhpJobQueue\Tests\Mock\PhpJobQueue;

class AbstractWorker extends AbstractWorkerOriginal
{
    public function work()
    {
    }
}

class AbstractWorkerTest extends TestCase
{
    protected $worker;
    protected $pjq;
    protected $storage;
    
    public function setUp()
    {
        parent::setUp();
        $this->pjq = new PhpJobQueue();
        $this->storage = $this->getRedisStorageMock();
        $this->worker = new AbstractWorker($this->pjq, $this->storage, 'worker.test');
    }
    
    public function testUpdateProcLine()
    {
        $this->replaceFunction('setproctitle', '$title');
        AbstractWorker::updateProcline('foo');
        $this->assertEquals(1, TestCase::getFunctionCalled('setproctitle'));
    }
    
    /**
     * @covers PhpJobQueue\Worker\AbstractWorker::setQueuesFilter
     */
    public function testSetQueuesFilter()
    {
        $worker = new AbstractWorker($this->pjq, $this->storage, 'worker.test');
        $worker->setQueuesFilter(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $worker->getQueuesFilter());
    }
    
    /**
     * @covers PhpJobQueue\Worker\AbstractWorker::getLogger
     */
    public function testGetLogger()
    {
        $this->assertInstanceOf('Monolog\\Logger', $this->worker->getLogger());   
    }
}
