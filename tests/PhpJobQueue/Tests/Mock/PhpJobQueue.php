<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Mock;

class PhpJobQueue extends \PhpJobQueue\PhpJobQueue
{
    protected $handler;
    
    public function attachLogHandlers(\Monolog\Logger $logger)
    {
        if (!isset($this->handler)) {
            $this->handler = new MonologTestingHandler(new \Monolog\Handler\NullHandler());
        }
        
        $logger->pushHandler($this->handler);
        return $logger;        
    }
}