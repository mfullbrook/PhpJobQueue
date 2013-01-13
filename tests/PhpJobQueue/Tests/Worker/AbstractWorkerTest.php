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
    /* @var \PHPUnit_Framework_MockObject_MockObject */
    protected $storage;
    
    public function setUp()
    {
        parent::setUp();
        $this->pjq = new PhpJobQueue();
        $this->storage = $this->getStorageMock();
        $this->worker = new AbstractWorker($this->pjq, $this->storage, 'worker.test');
    }

    /**
     * @covers PhpJobQueue\Worker\AbstractWorker::updateProcLine
     */
    public function testUpdateProcLine()
    {
        $this->replaceFunction('setproctitle', '$title');
        AbstractWorker::updateProcline('foo');
        $this->assertEquals(1, TestCase::getFunctionCalled('setproctitle'));
    }
    
    /**
     * @covers PhpJobQueue\Worker\AbstractWorker::setQueuesFilter
     * @covers PhpJobQueue\Worker\AbstractWorker::getQueuesFilter
     */
    public function testSetQueuesFilter()
    {
        $this->worker->setQueuesFilter(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->worker->getQueuesFilter());
    }
    
    /**
     * @covers PhpJobQueue\Worker\AbstractWorker::getLogger
     */
    public function testGetLogger()
    {
        $this->assertInstanceOf('Monolog\\Logger', $this->worker->getLogger());   
    }

    public function testStarted()
    {
        $this->storage
            ->expects($this->once())
            ->method('traceWorkerStatus')
            ->with($this->worker);
        $this->worker->started();
        $d = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->assertEquals($d->format(\DateTime::ISO8601), $this->worker->getStarted());
    }

    public function testStatus()
    {
        $this->storage
            ->expects($this->once())
            ->method('traceWorkerStatus')
            ->with($this->worker);
        $this->worker->status(AbstractWorker::STATUS_DESPATCHED);
        $this->assertEquals(AbstractWorker::STATUS_DESPATCHED, $this->worker->getStatus());
    }

    public function testWorked()
    {
        $this->storage
            ->expects($this->once())
            ->method('traceWorkerStatus')
            ->with($this->worker);
        $this->worker->worked();
        $this->assertEquals(1, $this->worker->getWorked());
    }
}
