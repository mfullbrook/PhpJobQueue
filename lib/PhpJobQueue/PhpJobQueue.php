<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue;

use PhpJobQueue\Config\ConfigurationInterface;
use PhpJobQueue\Config\Configuration;

/**
 * Main class for PhpJobQueue
 */
class PhpJobQueue
{
    const DEFAULT_QUEUE = 'default';
    
    protected $config;
    
    /**
     * Class constructor
     */
    public function __construct($config=null)
    {
        if ($config instanceof ConfigurationInterface) {
            $this->config = $config;
        } else {
            $this->config = new Configuration($config);
        }
        
    }
    
    /**
     * @return PhpJobQueue\Config\Configuration
     */
    public function getConfig($key = null)
    {
        return is_null($key) ? $this->config : $this->config->$key;
    }
    
    /**
     * Adds a job to the specified queue
     */
    public function enqueue(JobInterface $job, $queueName = self::DEFAULT_QUEUE)
    {
        
    }
}
