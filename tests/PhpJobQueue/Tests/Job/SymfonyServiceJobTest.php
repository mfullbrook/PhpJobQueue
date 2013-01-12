<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpJobQueue\Tests\Job;

use PhpJobQueue\Tests\TestCase;
use PhpJobQueue\Job\SymfonyServiceJob;
use PhpJobQueue\Tests\Mock\PhpJobQueue;
use PhpJobQueue\Exception\JobInvalidException;


class SymfonyServiceJobTest extends TestCase
{
    /**
     * @covers PhpJobQueue\Job\SymfonyServiceJob::validate
     */
    public function testAppPathPropertyException()
    {
        $this->setExpectedException(
            'PhpJobQueue\Exception\JobInvalidException',
            '[SymfonyServiceJob] The app path parameter is missing. Use $job->setAppPath()',
            JobInvalidException::NO_APP_PATH
        );

        $job = new SymfonyServiceJob();
        $job->setMethod('foo');
        $job->setEnvironment('foo');
        $job->setServiceId('foo');
        $job->validate();
    }

    /**
     * @covers PhpJobQueue\Job\SymfonyServiceJob::validate
     */
    public function testEnvironmentPropertyException()
    {
        $this->setExpectedException(
            'PhpJobQueue\Exception\JobInvalidException',
            '[SymfonyServiceJob] The environment parameter is missing. Use $job->setEnvironment()',
            JobInvalidException::NO_ENV
        );

        $job = new SymfonyServiceJob();
        $job->setEnvironment(null);
        $job->setMethod('foo');
        $job->setAppPath('foo');
        $job->setServiceId('foo');
        $job->validate();
    }

    /**
     * @covers PhpJobQueue\Job\SymfonyServiceJob::validate
     */
    public function testServiceIdPropertyException()
    {
        $this->setExpectedException(
            'PhpJobQueue\Exception\JobInvalidException',
            '[SymfonyServiceJob] The service ID parameter is missing. Use $job->setServiceId()',
            JobInvalidException::NO_SERVICE_ID
        );

        $job = new SymfonyServiceJob();
        $job->setMethod('foo');
        $job->setAppPath('foo');
        $job->setEnvironment('foo');
        $job->validate();
    }

    /**
     * @covers PhpJobQueue\Job\SymfonyServiceJob::validate
     */
    public function testMethodPropertyException()
    {
        $this->setExpectedException(
            'PhpJobQueue\Exception\JobInvalidException',
            '[SymfonyServiceJob] The method parameter is missing. Use $job->setMethod()',
            JobInvalidException::NO_METHOD
        );

        $job = new SymfonyServiceJob();
        $job->setServiceId('foo');
        $job->setAppPath('foo');
        $job->setEnvironment('foo');
        $job->validate();
    }

    /**
     * Test for each exception of the validate method, at the same time test the setters
     *
     * @covers PhpJobQueue\Job\SymfonyServiceJob::validate
     * @covers PhpJobQueue\Job\SymfonyServiceJob::setAppPath
     * @covers PhpJobQueue\Job\SymfonyServiceJob::setEnvironment
     * @covers PhpJobQueue\Job\SymfonyServiceJob::setServiceId
     * @covers PhpJobQueue\Job\SymfonyServiceJob::setMethod
     * @covers PhpJobQueue\Job\SymfonyServiceJob::setDebug
     */
    public function testSettersAndValidate()
    {
        $job = new SymfonyServiceJob();
        $job->setAppPath('/test/path');
        $job->setEnvironment('test');
        $job->setServiceId('foo');
        $job->setMethod('bar', array(123));
        $job->setDebug(true);
        $job->validate();

        // check that all the setters set the values correctly
        $this->assertEquals(array(
            'app_path' => '/test/path',
            'env' => 'test',
            'service_id' => 'foo',
            'method' => 'bar',
            'method_args' => array(123),
            'debug' => true
        ), $job->getParameters());
    }
    
    /**
     * @covers PhpJobQueue\Job\SymfonyServiceJob::perform
     */
    public function testPerform()
    {
        $worker = $this->getMockBuilder('PhpJobQueue\\Worker\\AbstractWorker')
            ->disableOriginalConstructor()
            ->getMock();

        $job = new SymfonyServiceJob();
        $job->setParameters(array(
            'app_path' => __DIR__ . '/resources',
            'env' => 'prod',
            'debug' => true,
            'service_id' => 'foo_service',
            'method' => 'serviceTest',
            'method_args' => array('cuz', 'zap')
        ));
        $job->perform($worker);

        $this->assertEquals(1, \AppKernel::$instance->bootCalled);
        $this->assertEquals('foo_service', \AppKernel::$instance->getContainer()->getService);
        $this->assertEquals(array('cuz', 'zap'), \AppKernel::$instance->getContainer()->service->args);
    }
}