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

namespace jubianchi\async;

require_once __DIR__ . '/runtime.php';

final class pipe extends \splQueue
{
    public function __invoke($data)
    {
        $this->enqueue($data);
    }

    public function dequeue() : \generator
    {
        while ($this->count() === 0) {
            yield;
        }

        return parent::dequeue();
    }
}
