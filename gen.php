<?php

require_once __DIR__ . '/vendor/autoload.php';

use function jubianchi\async\runtime\{await, delay, all, race, some};

function spawn($cmd) {
    $pipes = [];

    $proc = proc_open(
        $cmd,
        [
            0 => array('pipe', 'r'),
            1 => array('pipe', 'w'),
            2 => array('pipe', 'w'),
        ],
        $pipes
    );

    $cancel = false;

    while (proc_get_status($proc)['running'] === true && $cancel != false) {
        $cancel = yield;
    }

    proc_close($proc);

    return stream_get_contents($pipes[1]);
}

$start = microtime(true);

var_dump(await(
    race(
        spawn('sleep 2 && echo -n "First / Slower"'),
        spawn('sleep 1 && echo -n "Second / Faster"')
    )
));

var_dump(microtime(true) - $start);
