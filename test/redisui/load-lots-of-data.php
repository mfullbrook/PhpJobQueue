<?php

include 'bootstrap.php';

for ($i=0; $i<100000; $i++)
{
    $key = sha1(uniqid());
    switch (rand(1,5)) {
        case 1: // string
            $client->set($key, $key);
            break;
        case 2: // list
            $client->lpush($key, $key);
            break;
        case 3:
            
    }
    
    
}

took('to add 100000 random keys using sha1');