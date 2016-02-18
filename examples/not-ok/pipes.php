<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\pipe\{make};
use function jubianchi\async\runtime\{await, all, fork};
use function jubianchi\async\time\{delay, throttle};


$pipe = make();
$i = 0;
await(
    all(
        throttle(2500, function() use ($pipe, &$i) { $pipe->enqueue($i++); }),
        throttle(500, function() use ($pipe) { echo __LINE__; var_dump("\033[32m" . (yield from $pipe->dequeue()) . "\033[0m"); }),
        throttle(1000, function() use ($pipe) { echo __LINE__; var_dump("\033[31m" . (yield from $pipe->dequeue()) . "\033[0m"); })
    )
);
