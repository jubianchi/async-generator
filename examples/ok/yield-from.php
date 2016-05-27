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

use function jubianchi\async\runtime\{await, all, wrap};
use function jubianchi\async\time\{delay};

$start = microtime(true);

function second() {
    var_dump(__FUNCTION__ . ' - ' . 3);
    yield from delay(1000);
    var_dump(__FUNCTION__ . ' - ' . 4);
}

function first() {
    var_dump(__FUNCTION__ . ' - ' . 1);
    yield from delay(1000);
    var_dump(__FUNCTION__ . ' - ' . 2);
    yield from delay(1000);
    yield from second();

    return 5;
}

var_dump(await(all(first(), second())));


echo 'Time spent: ' . ($with = microtime(true) - $start) . PHP_EOL;
