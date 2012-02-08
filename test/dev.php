<?php


include __DIR__.'/../autoload.php';

$queue = new PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');

$job = new PhpJobQueue\Job\CommandJob('date');

$queue->enqueue($job);
