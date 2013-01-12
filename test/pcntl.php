<?php
$i = 0;

for ($x=2; $x>0; $x--) {
    if (($pid = pcntl_fork()) == 0) {
        echo 'child '.++$i."\n";
        sleep(10);
        exit();
    }
}

pcntl_wait($status);