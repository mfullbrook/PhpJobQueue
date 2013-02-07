<?php


include __DIR__.'/../vendor/autoload.php';

$pjq = new Mcf\PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');

$job = new Mcf\PhpJobQueue\Job\CommandJob();
$job->setCommand('sleep 10');

$pjq->enqueue($job);


printf("New Job Count: %d\n", $pjq['default']->countJobs());