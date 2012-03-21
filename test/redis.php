<?php

include __DIR__.'/../autoload.php';

$redis = new Predis\Client();

var_dump($redis->get('foo'));