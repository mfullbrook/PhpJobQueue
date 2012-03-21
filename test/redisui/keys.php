<?php

include 'bootstrap.php';

print 'DBSIZE = '.$client->dbsize()."\n";

$keys = $client->keys('*');
took('retrieve all the keys');

sort($keys);
took('sort all the keys');

// add the keys to sorted set
foreach ($keys as $position => $key) {
    $client->zadd('keys.sorted', $position, $key);
}
took('to add all the keys to a sorted set');

/*
key
  ttl
  type

ordered by key alphabetically

loop through to find by type
