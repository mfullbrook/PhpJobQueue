<?php

define('TEST_RESOURCES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);

$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('Mcf', __DIR__);
/*
$classLoader->registerNamespaces(array(
    'Mcf\\PhpJobQueue' => array(__DIR__.'/../src', __DIR__),
    'PhpJobQueue\\Tests' => __DIR__
));

// enable Debug loader
require __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/DebugUniversalClassLoader.php';
Symfony\Component\ClassLoader\DebugUniversalClassLoader::enable();
*/
