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

namespace jubianchi\async\socket;

require_once __DIR__ . '/runtime.php';

use jubianchi\async\runtime;

function write($socket, $data, $length = null) : \generator
{
    if (is_resource($socket) === false) {
        throw new \invalidArgumentException();
    }

    $cancel = false;
    $written = 0;

    while ($written < strlen($data) && $cancel == false) {
        $write = [$socket];
        $read = $except = null;

        $select = socket_select($read, $write, $except, 0);

        if ($select > 0) {
            $written += socket_write($write[0], substr($data, $written), $length ?: -1);
        }

        $cancel = yield;
    }
}
