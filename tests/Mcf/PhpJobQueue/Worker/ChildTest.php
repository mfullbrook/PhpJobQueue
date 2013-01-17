<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Worker;

use Mcf\PhpJobQueue\TestCase;
use Mcf\PhpJobQueue\Mock\PhpJobQueue;
use Mcf\PhpJobQueue\Worker\Child as OriginalChild;

class TestChild extends OriginalChild
{
    public function retrieveJob()
    {
        return parent::retrieveJob();
    }

    public function perform(\Mcf\PhpJobQueue\Job\Job $job)
    {
        return parent::perform($job);
    }
}

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
        $this->child = new TestChild($this->pjq, $this->storage, false);
    }
    
    public function testConstructor()
    {
        // forked
        $child = new TestChild($this->pjq, $this->storage, true);
        $context = $child->getContext();
        $this->assertEquals(posix_getppid(), $context['ppid']);
        
        // not forked
        $child = new TestChild($this->pjq, $this->storage, false);
        $context = $child->getContext();
        $this->assertNull($context['ppid']);
    }
    
    public function testRetrieveJob()
    {
        $this->assertNull($this->child->retrieveJob());
    }
}

