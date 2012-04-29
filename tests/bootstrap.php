<?php

include __DIR__ . '/../autoload.php';

define('TEST_RESOURCES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);

$classLoader->registerNamespaces(array(
    'PhpJobQueue\\Tests' => __DIR__
));

// enable Debug loader
require __DIR__.'/../vendor/symfony/src/Symfony/Component/ClassLoader/DebugUniversalClassLoader.php';
Symfony\Component\ClassLoader\DebugUniversalClassLoader::enable();

