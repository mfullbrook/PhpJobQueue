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

use Mcf\PhpJobQueue\Config\RedisConfig;

class RedisConfigTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsInvalidArgumentException()
    {
        $config = new RedisConfig();
        $config->initialise('not an array');
    }
    
    public function testConstructor()
    {
        $config = new RedisConfig();
        $this->assertEquals(array('tcp://127.0.0.1:6379'), $config->getParameters());
        $this->assertEquals(array('prefix' => RedisConfig::DEFAULT_PREFIX), $config->getOptions());
    }
    
    public function testParameters()
    {
        $config = new RedisConfig();
        $config->initialise(array('parameters' => array('tcp://10.10.10.10:6380')));
        $this->assertEquals(array('tcp://10.10.10.10:6380'), $config->getParameters());
    }
    
    public function testOptions()
    {
        $config = new RedisConfig();
        $config->initialise(array('options' => array('prefix' => 'new')));
        $this->assertEquals(array('prefix' => 'new'), $config->getOptions());
    }
}   
