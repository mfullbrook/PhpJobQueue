<?php
namespace
{
    class AppKernel
    {
        public static $instance;
        public $bootCalled = 0;
        public $container;

        public function __construct()
        {
            self::$instance = $this;
            $this->container = new Mock_Container();
        }

        public function boot()
        {
            $this->bootCalled++;
        }

        public function getContainer()
        {
            return $this->container;
        }
    }

    class Mock_Container
    {
        public $service;
        public $getService;

        public function get($service)
        {
            $this->getService = $service;
            return $this->service = new Mock_Service();
        }
    }

    class Mock_Service
    {
        public $args;

        public function serviceTest()
        {
            $this->args = func_get_args();
        }
    }
}