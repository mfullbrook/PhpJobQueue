<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Worker;

use PhpJobQueue\PhpJobQueue;

/**
 * Child Worker. Processes a job one at a time
 */
class Child extends AbstractWorker
{

    
    /**
     * Class Constructor
     *
     * @param PhpJobQueue $phpJobQueue The core PhpJobQueue object
     * @param int $processId The process ID assigned to this worker
     */
    public function __construct(PhpJobQueue $phpJobQueue)
    {
        parent::__construct($phpJobQueue, 'worker.child');
        $this->logger->debug(sprintf('Forked with PID %s by PPID %s', posix_getpid(), posix_getppid()));
    }
    
    
}
