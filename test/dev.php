<?php


include __DIR__.'/../autoload.php';

$queue = new PhpJobQueue\PhpJobQueue(__DIR__.'/config.yml');

$client = $queue->getConfig('redis')->getClient();
$client->set('library', 'predis');
$retval = $client->get('library');

var_dump($retval);
