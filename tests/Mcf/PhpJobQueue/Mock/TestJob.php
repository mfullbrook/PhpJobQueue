<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Mock;

use Mcf\PhpJobQueue\Job\AbstractJob;

class TestJob extends AbstractJob
{
    public function perform(\Mcf\PhpJobQueue\Worker\AbstractWorker $worker)
    {
    }
    
    public function validate()
    {
    }
}