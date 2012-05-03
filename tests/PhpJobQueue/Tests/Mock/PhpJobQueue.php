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
    
    public function getBufferedLogs($filterLevel = null)
    {
        $buffer = $this->handler->getBuffer();
        $filtered = array();
        
        for ($i=0; $i<count($buffer); $i++) {
            // skip any items that have a level lower than the filter param
            if ($filterLevel && $buffer[$i]['level'] < $filterLevel) {
                continue;
            }
            // strip the the time stamp and potentially empty stuff
            unset($buffer[$i]['datetime']);
            if (empty($buffer[$i]['context'])) {
                unset($buffer[$i]['context']);
            }
            if (empty($buffer[$i]['extra'])) {
                unset($buffer[$i]['extra']);
            }
            
            $filtered[] = $buffer[$i];
        }
        
        return $filtered;
    }
}