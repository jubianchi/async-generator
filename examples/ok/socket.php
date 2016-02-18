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

use function jubianchi\async\loop\{endless};
use function jubianchi\async\pipe\{make};
use function jubianchi\async\runtime\{await, all};
use function jubianchi\async\socket\{write};
use function jubianchi\async\stream\{tail};

$pipe = make();
$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

socket_connect($socket, 0, $argv[1]);
socket_set_nonblock($socket);

await(
    all(
        tail(fopen(__DIR__ . '/../data/first.log', 'r'), $pipe),
        tail(fopen(__DIR__ . '/../data/second.log', 'r'), $pipe),
        endless(function() use ($socket, $pipe) {
            $data = yield from $pipe->dequeue();

            yield from write($socket, $data);
        })
    )
);
