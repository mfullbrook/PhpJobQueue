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

use Symfony\Component\Yaml\Yaml;

/**
 * Central Configuration class.
 */
class Configuration implements ConfigurationInterface
{
    protected $config;
    
    public function __construct($input = null)
    {
        $this->loadHandlers();
        $this->initialise($input);
    }
    
    public function loadHandlers()
    {
        $this->configHandlers = array(
            'redis' => new RedisConfig(),
            'queues' => new QueuesConfig(),
        );
    }
    
    /**
     * Processes the input, which can be a path to a YAML file, YAML string or an array of options
     */
    public function initialise($input = null)
    {
        // Yaml component will process the input correctly whether it is a path, YAML string or array.
        $this->options = Yaml::parse($input);
        
        if (!is_array($this->options)) {
            $this->options = array();
            return null;
        }
        
        // let handlers process input
        foreach ($this->options as $key => $value) {
            if (isset($this->configHandlers[$key])) {
                $this->configHandlers[$key]->initialise($value);
            }
        }
    }
    
    public function __get($option)
    {
        if (isset($this->configHandlers[$option])) {
            return $this->configHandlers[$option];
        }
        
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
        
        return null;
    }
}
