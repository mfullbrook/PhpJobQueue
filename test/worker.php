<?php

include __DIR__.'/../autoload.php';

$pjq = new PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');
$pjq->work(2);