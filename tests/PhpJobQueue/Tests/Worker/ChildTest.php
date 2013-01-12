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
use PhpJobQueue\Tests\Mock\PhpJobQueue;
use PhpJobQueue\Worker\Child as OriginalChild;

class ChildTest extends TestCase
{
    protected $pjq;
    protected $storage;
    protected $child;
    
    public function setUp()
    {
        parent::setUp();
        $this->pjq = new PhpJobQueue();
        $this->storage = $this->getRedisStorageMock();
        $this->child = new Child($this->pjq, $this->storage, false);
    }
    
    public function testConstructor()
    {
        // forked
        $child = new Child($this->pjq, $this->storage, true);
        $context = $child->getContext();
        $this->assertEquals(posix_getppid(), $context['ppid']);
        
        // not forked
        $child = new Child($this->pjq, $this->storage, false);
        $context = $child->getContext();
        $this->assertNull($context['ppid']);
    }
    
    public function testRetrieveJob()
    {
        $this->assertNull($this->child->retrieveJob());
    }
}

class Child extends OriginalChild
{
    public function retrieveJob()
    {
        return parent::retrieveJob();
    }
    
    public function perform(\PhpJobQueue\Job\Job $job)
    {
        return parent::perform($job);
    }
}