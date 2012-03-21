<?php


include __DIR__.'/../autoload.php';

$pjq = new PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');

$job = new PhpJobQueue\Job\CommandJob();
$job->setCommand('sleep 10');

$pjq->enqueue($job);


printf("New Job Count: %d\n", $pjq['default']->countJobs());