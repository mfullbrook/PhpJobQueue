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

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected static $functionCalls;
    protected static $replacedFunctions;
    
    public function setUp()
    {
        if (!extension_loaded('runkit')) {
            echo "These unit tests require the runkit extension.  For more information, please visit:\n";
            echo "https://github.com/zenovich/runkit/\n";
            exit(1);
        }
        
        // zero function counter
        self::$functionCalls = array();
        self::$replacedFunctions = array();
    }
    
    public function tearDown()
    {
        $this->revertFunctions();
    }
    
    protected function loggerFactory()
    {
        $logger = new \Monolog\Logger('unittest');
        $buffer = new \Monolog\Handler\BufferHandler(new \Monolog\Handler\NullHandler());
        $logger->pushHandler($buffer);
    }
    
    protected function replaceFunction($name, $args = '', $code = '')
    {
        $backup = '_backup_'.$name;
        
        $code = "\PhpJobQueue\Tests\TestCase::functionCalled(__FUNCTION__); $code";
        
        if (function_exists($name)) {
            $renamed = true;
            \runkit_function_copy($name, $backup);
            \runkit_function_redefine($name, $args, $code);
        } else {
            $renamed = false;
            \runkit_function_add($name, $args, $code);
        }
        
        self::$replacedFunctions[] = array($name, $renamed ? $backup : false);
    }
    
    protected function revertFunctions()
    {
        foreach (self::$replacedFunctions as $names) {
            list($original, $backup) = $names;
            \runkit_function_remove($original);
            if ($backup) {
                \runkit_function_copy($backup, $original);
                \runkit_function_remove($backup);
            }
        }
    }
    
    public static function functionCalled($name)
    {
        if (!isset(self::$functionCalls[$name])) {
            self::$functionCalls[$name] = 0;
        }
        self::$functionCalls[$name]++;
    }
    
    public static function getFunctionCalled($name)
    {
        return isset(self::$functionCalls[$name]) ? self::$functionCalls[$name] : 0;
    }
    
    protected function getRedisStorageMock(Array $methods = array())
    {
        return $this->getMockBuilder('PhpJobQueue\\Storage\\Redis')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();
    }
}