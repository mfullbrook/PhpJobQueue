<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Exception;

class JobCorruptException extends \Exception
{
    const EMPTY_CLASS = 1;
    const EMPTY_PARAMS = 2;
    const JSON_DECODE_FAILED = 3;
}