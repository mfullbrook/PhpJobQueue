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
    
    protected function replaceFunction($name, $args, $code)
    {
        do {
            $backup = '_' . ($backup ? $backup : $name);
        } while (function_exists($backup));
        
        $code = "TestCase::\$functionCalls[] = $name; $code";
        
        if (function_exists($name)) {
            $renamed = true;
            runkit_function_rename($name, $backup);
        } else {
            $renamed = false;
        }
        runkit_function_define($name, $args, $code);
        
        self::$replacedFunctions[] = array($name, $renamed ? $backup : false);
    }
    
    protected function revertFunctions()
    {
        foreach (self::$replacedFunctions as $names) {
            list($original, $backup) = $names;
            runkit_function_remove($original);
            if ($backup) {
                runkit_function_rename($backup, $original);
            }
        }
    }
}