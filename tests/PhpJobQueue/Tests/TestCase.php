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
    protected function loggerFactory()
    {
        $logger = new \Monolog\Logger('unittest');
        $buffer = new \Monolog\Handler\BufferHandler(new \Monolog\Handler\NullHandler());
        $logger->pushHandler($buffer);
    }
}