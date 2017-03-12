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

use function jubianchi\async\runtime\{await, all};
use function jubianchi\async\stream\{read};
use function jubianchi\async\time\{delay};

const BUFFER_LENGTH = 2048;

$start = microtime(true);

$f1 = fopen(__DIR__ . '/../data/d1.dat', 'r');
$f2 = fopen(__DIR__ . '/../data/d2.dat', 'r');

$length = 0;

while ($buffer = fread($f1, BUFFER_LENGTH)) {
    $length += strlen($buffer);
}

while ($buffer = fread($f2, BUFFER_LENGTH)) {
    $length += strlen($buffer);
}

fclose($f1);
fclose($f2);

echo 'Found ' . $length . ' characters' . PHP_EOL;
echo 'Time spent without await: ' . ($without = microtime(true) - $start) . PHP_EOL;

$start = microtime(true);

$f1 = fopen(__DIR__ . '/../data/d1.dat', 'r');
$f2 = fopen(__DIR__ . '/../data/d2.dat', 'r');

$length = 0;

await(
    all(
        read($f1, function($data) use (& $length) { $length += strlen($data); }, BUFFER_LENGTH),
        read($f2, function($data) use (& $length) { $length += strlen($data); }, BUFFER_LENGTH)
    )
);

echo 'Found ' . $length . ' characters' . PHP_EOL;
echo 'Time spent with await: ' . ($with = microtime(true) - $start) . PHP_EOL;

if ($without > $with) {
    echo 'await is ' . round(($without / $with), 2) . ' times faster' . PHP_EOL;
} else {
    echo 'await is ' . round(($with / $without), 2) . ' times slower' . PHP_EOL;
}
