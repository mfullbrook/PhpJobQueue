<?php 

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Worker;

class TraceInfo
{
    public $pid;
    public $type;
    public $started;
    public $status;
    public $worked;
    
    public function __construct($pid, $type, $started, $status, $worked)
    {
        $this->pid = $pid;
        $this->type = $type;
        $this->started = $started;
        $this->status = $status;
        $this->worked = $worked;
    }
    
    public function toArray()
    {
        return array(
            'pid' => $this->pid,
            'type' => $this->type,
            'started' => $this->started,
            'status' => $this->status,
            'worked' => $this->worked,
        );
    }
    
    public static function fromArray($arr)
    {
        return new self($arr['pid'], $arr['type'], $arr['started'], $arr['status'], $arr['worked']);
    }
}