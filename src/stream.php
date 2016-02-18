<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

declare (strict_types = 1);

namespace jubianchi\async\stream;

require_once __DIR__ . '/runtime.php';

use jubianchi\async\runtime;

function tail($stream, callable $data = null, $length = null) : \generator
{
    if (is_resource($stream) === false) {
        throw new \invalidArgumentException();
    }

    $data = $data ?? function () {};
    $cancel = false;

    while ($cancel == false) {
        $read = [$stream];
        $write = $except = null;

        $select = stream_select($read, $write, $except, 0);

        if ($select > 0) {
            while ($buffer = stream_get_contents($read[0], $length ?: -1)) {
                $data($buffer);
            }
        }

        $cancel = yield;
    }
}

function read($stream, callable $data = null, $length = null) : \generator
{
    if (is_resource($stream) === false) {
        throw new \invalidArgumentException();
    }

    $data = $data ?? function () {};

    do {
        $read = [$stream];
        $write = $except = null;

        $select = stream_select($read, $write, $except, 0);

        if ($select > 0) {
            $buffer = stream_get_contents($read[0], $length ?: -1);

            if ($buffer !== '') {
                runtime::await($data($buffer));
            }
        }

        $cancel = yield;

        $metadata = stream_get_meta_data($stream);
    } while (($metadata['eof'] === false || $metadata['unread_bytes'] > 0) && $cancel == false);
}

function write($stream, $data, $length = null) : \generator
{
    if (is_resource($stream) === false) {
        throw new \invalidArgumentException();
    }

    $cancel = false;
    $written = 0;

    while ($written < strlen($data) && $cancel == false) {
        $write = [$stream];
        $read = $except = null;

        $select = stream_select($read, $write, $exce, 0);

        if ($select > 0) {
            $written += fwrite($write[0], substr($data, $written), $length ?: -1);
        }

        $cancel = yield;
    }
}
