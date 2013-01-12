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

class ExceptionsTest extends TestCase
{
    /**
     * Basically for code coverage of empty exception classes
     */
    public function testConstruction()
    {
        $e = new \PhpJobQueue\Exception\JobCorruptException();
        $e = new \PhpJobQueue\Exception\JobInvalidException();
        $e = new \PhpJobQueue\Exception\JobNotFoundException();
        $e = new \PhpJobQueue\Exception\QueueNotFoundException();
        $this->assertTrue(true); // silence the no test warning
    }
}