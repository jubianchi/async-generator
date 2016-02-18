<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . '/../vendor/autoload.php';

use function jubianchi\async\runtime\{await, race};
use function jubianchi\async\time\{delay, throttle};

$start = microtime(true);

await(
    race(
        delay(3000),
        throttle(500, function() { var_dump(__LINE__); }),
        throttle(1000, function() { var_dump(__LINE__); })
    )
);

echo 'Time spent: ' . ($with = microtime(true) - $start) . PHP_EOL;
