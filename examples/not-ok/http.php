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

use function jubianchi\async\runtime\{await, all, fork, race};
use function jubianchi\async\time\{delay};
use jubianchi\async\socket;

$address = '0.0.0.0';
$port = $_SERVER['argv'][1] ?? 1337;
$queue = [];

$start = function($socket, $address, $port) use (& $queue)
{
    socket_bind($socket, $address, $port);
    socket_listen($socket, 0);
    socket_set_nonblock($socket);
    $index = 0;
    $cancel = false;

    while ($cancel == false) {
        $client = socket_accept($socket);

        if ($client) {
            echo '> Got client...' . PHP_EOL;

            $queue[] = (function() use ($index, $client, $address, $port) {
                echo '> Handling request #' . $index . '...' . PHP_EOL;

                $response = 'Hello World!';
                $output = 'HTTP/1.1 200 OK' . "\r\n" .
                          'Date: ' . date("D, j M Y G:i:s T") . "\r\n" .
                          'Server: AsyncGenerator/1.0.0 (PHP ' . phpversion() . ')' . "\r\n" .
                          'Content-Length: ' . strlen($response) . "\r\n" .
                          'Content-Type: text/plain' . "\r\n" .
                          "\r\n" .
                          $response . "\r\n";

                yield from delay(1000);
                yield from socket\write($client, $output, 5);

                socket_close($client);
            })();

            echo '> Client request #' . $index++ . ' queued...' . PHP_EOL;
        }

        $cancel = yield;
    }

    socket_close($socket);
};

await(
    race(
        $start(socket_create(AF_INET, SOCK_STREAM, 0), $address, $port),
        fork($queue)
        //delay(20000)
    )
);
