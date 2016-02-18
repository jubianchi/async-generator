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

use function jubianchi\async\runtime\{await, all, wrap};
use function jubianchi\async\time\{delay};

$start = microtime(true);

function two() {
    yield 3;
    yield 4;
}

function five() {
    yield 1;
    yield 2;
    yield from two();

    return 5;
}

$generators = await(
    all(
        wrap(five()),
        wrap(two())
    )
);

foreach ($generators as $generator) {
    foreach ($generator as $v) {
        var_dump($v);
    }

    var_dump('return: ' . $generator->getReturn());
}

echo 'Time spent: ' . ($with = microtime(true) - $start) . PHP_EOL;
