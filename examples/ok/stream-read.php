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

use function jubianchi\async\runtime\{await, all};
use function jubianchi\async\stream\{read};

var_dump(
    await(
        all(
            read(fopen(__DIR__ . '/data/tiny.dat', 'r'), function($d) { var_dump($d); }, 1),
            read(fopen(__DIR__ . '/data/d1.dat', 'r'), function($d) { var_dump($d); }, 10)
        )
    )
);
