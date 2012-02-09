<?php

include __DIR__.'/../autoload.php';

$pjq = new PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');

$worker = new PhpJobQueue\Worker\Manager($pjq, 5);
$worker->work();