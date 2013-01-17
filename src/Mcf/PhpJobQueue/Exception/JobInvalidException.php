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

class JobInvalidException extends \Exception
{
    const NO_APP_PATH = 1;
    const NO_SERVICE_ID = 2;
    const NO_METHOD = 3;
    const NO_ENV = 4;
}