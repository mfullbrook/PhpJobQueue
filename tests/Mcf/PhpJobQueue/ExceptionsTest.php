<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue;
use Mcf\PhpJobQueue\Exception;

class ExceptionsTest extends \Mcf\PhpJobQueue\TestCase
{
    /**
     * Basically for code coverage of empty exception classes
     */
    public function testConstruction()
    {
        $e = new Exception\JobCorruptException();
        $e = new Exception\JobInvalidException();
        $e = new Exception\JobNotFoundException();
        $e = new Exception\QueueNotFoundException();
        $this->assertTrue(true); // silence the no test warning
    }
}