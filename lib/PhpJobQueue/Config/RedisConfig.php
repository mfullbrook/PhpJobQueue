<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Config;

use Predis;

/**
 * Redis config
 */
class RedisConfig
{
    protected $parameters;
    
    protected $options;
    
    /** 
     * @var Predis\Client
     */
    protected $client;
    
    /**
     * Set the defaults
     */
    public function __construct()
    {
        $this->parameters = array(
            'tcp://127.0.0.1:6379'
        );

        $this->options = array();
    }
    
    public function processInput($input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException('The `redis` configuration section is invalid.');
        }
        
        if (isset($input['parameters'])) {
            $this->parameters = $input['parameters'];
        }
        if (isset($input['options'])) {
            $this->options = $input['options'];
        }
    }
    
    public function getClient()
    {
        if (!isset($this->client)) {
            $this->client = new Predis\Client($this->parameters, $this->options);
        }
        return $this->client;
    }
}
