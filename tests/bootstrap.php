<?php

include __DIR__ . '/../autoload.php';

define('TEST_RESOURCES_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'resources' . DIRECTORY_SEPARATOR);

// enable Debug loader
Symfony\Component\ClassLoader\DebugUniversalClassLoader::enable();
