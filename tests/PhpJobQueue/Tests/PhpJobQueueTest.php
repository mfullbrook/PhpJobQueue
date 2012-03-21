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

use PhpJobQueue\PhpJobQueue;
use PhpJobQueue\Config\Configuration;
use PhpJobQueue\Config\RedisConfig;

class PhpJobQueueTest extends \PHPUnit_Framework_Testcase
{
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
    
    private function PhpJobQueueFactory()
    {
        $this->pjq = new PhpJobQueue(array(
            
        ));
        $this->pjq->setClass('queue', ''); // stub
        $this->pjq->setClass('storage', ''); // stub
    }
    
    public function testSetUnknownClassKey()
    {
        $this
    }
}