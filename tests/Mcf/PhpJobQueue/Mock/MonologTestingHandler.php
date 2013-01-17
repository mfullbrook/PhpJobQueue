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

use Monolog\Handler\BufferHandler;

class MonologTestingHandler extends BufferHandler
{
    public function handle(array $record)
    {
        parent::handle($record);
    }
    
    public function close()
    {
        // do nothing
    }
    
    public function getBuffer()
    {
        return $this->buffer;
    }
}
