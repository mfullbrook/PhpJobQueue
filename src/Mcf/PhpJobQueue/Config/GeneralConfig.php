<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mcf\PhpJobQueue\Config;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\NullHandler;

/**
 * Provides General Config options
 *
 * The options are protected but accessed as though they are public properties.
 */
class GeneralConfig
{
    const LOG_LEVEL_DEBUG = 100;
    const LOG_LEVEL_INFO = 200;
    const LOG_LEVEL_WARNING = 300;
    const LOG_LEVEL_ERROR = 400;
    const LOG_LEVEL_CRITICAL = 500;
    const LOG_LEVEL_ALERT = 550;
    
    // set the options and their defaults...
    
    /**
     * number of seconds a successful job will remain in storage for. 0 = no expiry
     */
    protected $jobTtlSuccess = 3600; // 1 hour
    
    /**
     * number of seconds a failed job will remain in storage for. 0 = no expiry
     */
    protected $jobTtlFailure = 604800; // 7 * 24 * 3600 = 1 week
    
    /**
     * is the logging feature enabled
     */
    protected $logEnabled = true;

    /**
     * can only be one of self::$logLevel, but in yaml config use the words: debug, info, warning, error, critical, alert
     */
    protected $logLevel = \Monolog\Logger::ERROR;
    
    /**
     * if the path is not a full path then it is relative to PHPJOBQUEUE_ROOT (if defined)
     */
    protected $logPath = 'phpjobqueue.log';
    
    /**
     * The number of seconds a work should wait before reattempting to find a job
     */
    protected $workerInterval = 5;
    
    /**
     * The duration after which a child worker will shutdown
     */
    protected $workerMaxDuration = 600;
    
    /**
     * The number of jobs a child worker will process before shutting down
     */
    protected $workerMaxJobs = 200;
    

    // the following are not options...
    
    /**
     * define what options are can be accessed from outside this class
     */
    protected static $publicOptions = array(
        'jobTtlSuccess',
        'jobTtlFailure',
        'logEnabled',
        'logLevel',
        'logPath',
        'workerInterval',
        'workerMaxDuration',
        'workerMaxJobs',
    );
    
    /**
     * The monolog handler stack
     *
     * @var array of Monolog\Handler\HandlerInterface
     */
    protected $logHandlers;
    
    
    
    /**
     * log levels (copied from Monolog\Logger as the property in Logger is protected)
     */
    protected static $logLevels = array(
        100 => 'DEBUG',
        200 => 'INFO',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
    );
    
    /**
     * Process the input configuration
     *
     * @param array $input
     */
    public function initialise($input)
    {
        if (!is_array($input)) {
            throw new \InvalidArgumentException('The `general` configuration section is invalid.');
        }
        
        // process the TTLs
        if (isset($input['job_ttls'])) {
            foreach (array('success' => 'jobTtlSuccess', 'failure' => 'jobTtlFailure') as $key => $property) {
                if (array_key_exists($key, $input['job_ttls'])) {
                    $v = $input['job_ttls'][$key];
                    $this->$property = $v ? $v : false;
                }
            }
        }
        
        // log enabled?
        if (isset($input['log']['enabled']) && $input['log']['enabled']== false) {
            $this->logEnabled = false;
        }
        
        if ($this->logEnabled) {
            // process the log level
            if (isset($input['log']['level'])) {
                $level = array_search(strtoupper($input['log']['level']), self::$logLevels);
                if ($level === false) {
                    throw new \InvalidArgumentException('The `general.log.level` configuration options must be one of: debug, info, warning, error, critical or alert.');
                }
                $this->logLevel = $level;
            }
        
            // process the log path
            $path = isset($input['log']['path']) ? trim($input['log']['path']) : $this->logPath;
            if ((substr($path, 0, 1) != '/' || substr($path, 0, 1) == '.') && defined('PHPJOBQUEUE_ROOT')) {
                $path = PHPJOBQUEUE_ROOT . DIRECTORY_SEPARATOR . $path;
            }
            if (!file_exists(dirname($path))) {
                throw new \InvalidArgumentException("The log path `$path` does not exist (general.log.path).");
            }
            if (!is_writable(dirname($path))) {
                throw new \InvalidArgumentException("The log path `$path` is not writeable (general.log.path).");
            }
            $this->logPath = $path;
        }
        
        // process worker options
        if (isset($input['worker'])) {
            $w = $input['worker'];
            
            $optionsHash = array('interval' => 'workerInterval', 'max_duration' => 'workerMaxDuration', 'max_jobs' => 'workerMaxJobs');
            foreach ($optionsHash as $key => $property) {
                if (isset($w[$key]) && is_integer($w[$key]) && $w[$key] > 1) {
                    $this->$property = $w[$key];
                }
            }
        }
    }
    
    /**
     * Allow access to the options as properties
     */
    public function __get($option) {
        if (!in_array($option, self::$publicOptions)) {
            throw new \RuntimeException("Unable to find option '$option'");
        }
        return $this->$option;
    }
    
    
    /**
     * Initialises and attaches the monolog handlers to a logger instance
     *
     * @param Monolog\Logger $logger
     */ 
    public function attachLogHandlers(Logger $logger)
    {
        if (!isset($this->logHandlers)) {
            // initialise the monolog handlers
            if ($this->logEnabled) {
                $this->logHandlers[] = new StreamHandler($this->logPath, $this->logLevel);
            } else {
                $this->logHandlers[] = new NullHandler(\Monolog\Logger::DEBUG);
            }
        }
        
        foreach ($this->logHandlers as $handler) {
            $logger->pushHandler($handler);
        }
    }
    
}
