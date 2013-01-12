<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Job;

use PhpJobQueue\Worker\AbstractWorker;
use PhpJobQueue\Exception\JobInvalidException as Invalid;

/**
 * This class is used to run a job from within a symfony 2 environment
 *
 * The class is responsible for launching symfony
 */
class SymfonyServiceJob extends Job
{
    // set some defaults
    protected $parameters = array(
        'env' => 'prod',
        'debug' => false,
    );
    
    public function setAppPath($path)
    {
        $this->parameters['app_path'] = $path;
    }
    
    public function setServiceId($serviceId)
    {
        $this->parameters['service_id'] = $serviceId;
    }
    
    public function setMethod($method, $args = array())
    {
        $this->parameters['method'] = $method;
        $this->parameters['method_args'] = (array) $args;
    }

    /*
    public function setMethodArgs($args = array())
    {
        $this->parameters['method_args'] = (array) $args;
    }
    */
    
    public function setEnvironment($env)
    {
        $this->parameters['env'] = $env;
    }
    
    public function setDebug($debug)
    {
        $this->parameters['debug'] = (bool) $debug;
    }
    
    public function validate()
    {
        if (empty($this->parameters['app_path'])) {
            throw new Invalid('[SymfonyServiceJob] The app path parameter is missing. Use $job->setAppPath()', Invalid::NO_APP_PATH);
        }
        if (empty($this->parameters['env'])) {
            throw new Invalid('[SymfonyServiceJob] The environment parameter is missing. Use $job->setEnvironment()', Invalid::NO_ENV);
        }
        if (empty($this->parameters['service_id'])) {
            throw new Invalid('[SymfonyServiceJob] The service ID parameter is missing. Use $job->setServiceId()', Invalid::NO_SERVICE_ID);
        }
        if (empty($this->parameters['method'])) {
            throw new Invalid('[SymfonyServiceJob] The method parameter is missing. Use $job->setMethod()', Invalid::NO_METHOD);
        }
    }
    
    public function perform(AbstractWorker $worker)
    {
        $this->validate();

        // bootstrap and load the app's Kernel class
        require_once $this->parameters['app_path'] . '/bootstrap.php.cache';
        require_once $this->parameters['app_path'] . '/AppKernel.php';
        
        // boot the kerel
        $kernel = new \AppKernel($this->parameters['env'], (bool) $this->parameters['debug']);
        $kernel->boot();
        
        // get the service
        $service = $kernel->getContainer()->get($this->parameters['service_id']);
        
        // call the method
        call_user_func_array(array($service, $this->parameters['method']), $this->parameters['method_args']);
    }
}

