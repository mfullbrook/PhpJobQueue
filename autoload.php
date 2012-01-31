<?php

/*
 * This file is part of the PhpJobQueue package.
 *
 * (c) Mark Fullbrook <mark.fullbrook@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__.'/vendor/symfony/Symfony/Component/ClassLoader/UniversalClassLoader.php';
require __DIR__.'/vendor/symfony/Symfony/Component/ClassLoader/DebugUniversalClassLoader.php';

$classLoader = new Symfony\Component\ClassLoader\UniversalClassLoader();
$classLoader->registerNamespaces(array(
    'PhpJobQueue' => __DIR__.'/lib',
    'Symfony'     => __DIR__.'/vendor/symfony',
    'Predis'      => __DIR__.'/vendor/predis/lib',
));
$classLoader->register();

// enable Debug loader
Symfony\Component\ClassLoader\DebugUniversalClassLoader::enable();