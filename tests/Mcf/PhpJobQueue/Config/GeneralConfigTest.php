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

use Mcf\PhpJobQueue\Config\GeneralConfig;

class GeneralConfigTest extends \PHPUnit_Framework_Testcase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testThrowsInvalidArgumentException()
    {
        $config = new GeneralConfig();
        $config->initialise('not an array');
    }
    
    public function testInitialise()
    {
        $input = array(
            'job_ttls' => array(
                'success' => 86400,
                'failure' => 43200
            ),
            'log'=> array(
                'enabled' => true,
                'level' => 'debug',
                'path' =>  'test/test.log'
            ),
            'worker'=> array(
                'interval' => 2,
                'max_duration' => 20,
                'max_jobs' => 100
            )
        );
        
        $config = new GeneralConfig();
        $config->initialise($input);
        
        // assert all public exposed config properties
        $this->assertEquals(86400, $config->jobTtlSuccess, 'value of GeneralConfig->jobTtlSuccess is not correct');
        $this->assertEquals(43200, $config->jobTtlFailure, 'value of GeneralConfig->jobTtlFailure is not correct');
        $this->assertTrue($config->logEnabled, 'value of GeneralConfig->logEnabled is not correct');
        // 'debug' == 100
        $this->assertEquals(GeneralConfig::LOG_LEVEL_DEBUG, $config->logLevel, 'value of GeneralConfig->logLevel is not correct');
        $this->assertEquals(PHPJOBQUEUE_ROOT.'/test/test.log', $config->logPath, 'value of GeneralConfig->logPath is not correct');
        $this->assertEquals(2, $config->workerInterval, 'value of GeneralConfig->workerInterval is not correct');
        $this->assertEquals(20, $config->workerMaxDuration, 'value of GeneralConfig->workerMaxDuration is not correct');
        $this->assertEquals(100, $config->workerMaxJobs, 'value of GeneralConfig->workerMaxJobs is not correct');
    }
    
    public function testLogDisabled()
    {
        $config = new GeneralConfig();
        $config->initialise(array('log' => array('enabled' => false)));
        $this->assertFalse($config->logEnabled);
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testInvalidLogLevel()
    {
        $config = new GeneralConfig();
        $config->initialise(array('log' => array('level' => 'foo')));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testNonExistentLogPath()
    {
        $config = new GeneralConfig();
        $config->initialise(array('log' => array('path' => '/foo/bar')));
    }
    
    /**
     * @expectedException InvalidArgumentException
     */
    public function testUnwritableLogPath()
    {
        $config = new GeneralConfig();
        $config->initialise(array('log' => array('path' => '/bin')));
    }
    
    /**
     * @expectedException RuntimeException
     */
    public function testInvalidOption()
    {
        $config = new GeneralConfig();
        $config->foo;
    }
    
    public function testAttachLogHandlers()
    {
        $config = new GeneralConfig();
        $logger = new \Monolog\Logger('test');
        $config->attachLogHandlers($logger);
        $this->assertEquals($logger->popHandler(), new \Monolog\Handler\StreamHandler($config->logPath, $config->logLevel));
        
        $config = new GeneralConfig();
        $config->initialise(array('log' => array('enabled' => false)));
        $config->attachLogHandlers($logger);
        $this->assertEquals($logger->popHandler(), new \Monolog\Handler\NullHandler());
    }
}