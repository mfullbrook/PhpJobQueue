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

use Mcf\PhpJobQueue\Config\Configuration;
use Mcf\PhpJobQueue\Config\GeneralConfig;
use Mcf\PhpJobQueue\Config\QueuesConfig;
use Mcf\PhpJobQueue\Config\RedisConfig;

class ConfigurationTest extends \PHPUnit_Framework_Testcase
{
    public function testNullInput()
    {
        $configuration = new Configuration();
        $this->assertEquals(new GeneralConfig(), $configuration->general);
        $this->assertEquals(new QueuesConfig(), $configuration->queues);
        $this->assertEquals(new RedisConfig(), $configuration->redis);
    }
    
    public function testYamlInput()
    {
        $configuration = new Configuration(TEST_RESOURCES_PATH . 'test-config.yml');
        
        // assert general config
        $this->assertEquals(86400, $configuration->general->jobTtlSuccess, 'value of GeneralConfig->jobTtlSuccess is not correct');
        $this->assertEquals(43200, $configuration->general->jobTtlFailure, 'value of GeneralConfig->jobTtlFailure is not correct');
        // 'debug' == 100
        $this->assertEquals(100, $configuration->general->logLevel, 'value of GeneralConfig->logLevel is not correct');
        $this->assertEquals(PHPJOBQUEUE_ROOT.'/test/test.log', $configuration->general->logPath, 'value of GeneralConfig->logPath is not correct');
        $this->assertEquals(2, $configuration->general->workerInterval, 'value of GeneralConfig->workerInterval is not correct');
        $this->assertEquals(20, $configuration->general->workerMaxDuration, 'value of GeneralConfig->workerMaxDuration is not correct');
        $this->assertEquals(100, $configuration->general->workerMaxJobs, 'value of GeneralConfig->workerMaxJobs is not correct');
        
        // assert queues config
        
        
        // assert redis config
        $this->assertEquals(array('tcp://10.0.0.1:6380'), $configuration->redis->getParameters());
        $this->assertEquals(array('foo' => 'bar', 'prefix' => RedisConfig::DEFAULT_PREFIX), $configuration->redis->getOptions());
        
        // assert additional config
        $this->assertEquals(array('option' => 'test'), $configuration->extra);
        $this->assertNull($configuration->foobar);
    }
}