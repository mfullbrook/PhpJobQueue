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

/**
 * Redis config
 */
class RedisConfig
{
    const DEFAULT_PREFIX = 'PJQ:';
    
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
    
    public function initialise($input)
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
        
        // do we have a prefix?
        if (empty($this->options['prefix'])) {
            $this->options['prefix'] = self::DEFAULT_PREFIX;
        }
        
    }
    
    /**
     * Parameters getter
     */
    public function getParameters()
    {
        return $this->parameters;
    }
    
    /**
     * Options getter
     */
    public function getOptions()
    {
        return $this->options;
    }
}
