<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

$classLoader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$classLoader->registerNamespaces(array(
    'PhpJobQueue' => __DIR__.'/lib',
    'Symfony'     => __DIR__.'/vendor/symfony/src',
    'Predis'      => __DIR__.'/vendor/predis/lib',
    'Monolog'     => __DIR__.'/vendor/monolog/src',
));
$classLoader->register();

// define the root of the project
define('PHPJOBQUEUE_ROOT', __DIR__);