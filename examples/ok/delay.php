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
use function jubianchi\async\time\{delay};

$start = microtime(true);

var_dump(
    await(
        all(
            delay(5000, 'Hello'),
            delay(2000, 'World!')
        )
    )
);

echo 'Time spent: ' . ($with = microtime(true) - $start) . PHP_EOL;
