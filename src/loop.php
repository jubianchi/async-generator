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

class loop
{
    public static function whilst($condition, callable $resolve) : \generator
    {
        $cancel = false;

        if (is_callable($condition) === false) {
            $condition = function () use ($condition) { return $condition; };
        }

        while ($cancel == false && $condition()) {
            $resolved = $resolve();

            if ($resolved instanceof \generator) {
                yield from $resolved;
            }

            $cancel = yield;
        }
    }

    public static function until($condition, callable $resolve) : \generator
    {
        $condition = function () use ($condition) {
            return !(is_callable($condition) ? $condition() : $condition);
        };

        return self::whilst($condition, $resolve);
    }

    public static function times(int $times, callable $resolve) : \generator
    {
        $condition = function () use (&$times) {
            return $times-- > 0;
        };

        return self::whilst($condition, $resolve);
    }

    public static function endless($resolve) : \generator
    {
        yield from self::whilst(true, $resolve);
    }
}
