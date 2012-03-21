<?php

include __DIR__ . '/../../autoload.php';

use Predis\Client;

$client = new Client(array('database' => 1));

function resetTimer() {
    global $split;
    $split = microtime(true);
}


function took($action) {
    global $split;
    printf("It took %s seconds to %s\n", microtime(true)-$split, $action);
    $split = microtime(true);
}

resetTimer();
