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

class time
{
    /**
     * @param int   $timeout
     * @param mixed $resolve
     *
     * @return \generator
     */
    public static function delay(int $timeout, $resolve = null) : \generator
    {
        $timeout = self::ms2s($timeout);

        yield from self::wait(microtime(true), $timeout);

        return runtime::await($resolve);
    }

    /**
     * @param int   $interval
     * @param mixed $resolve
     *
     * @return \generator
     */
    public static function throttle(int $interval, $resolve) : \generator
    {
        $interval = self::ms2s($interval);
        $cancel = false;

        while ($cancel == false) {
            yield from self::wait(microtime(true), $interval);

            if ($cancel === true) {
                break;
            }

            if (is_callable($resolve)) {
                $resolved = $resolve();
            } else {
                $resolved = $resolve;
            }

            if ($resolved instanceof \generator) {
                yield from $resolved;
            }

            //runtime::await($resolve);

            $cancel = yield;
        }
    }

    /**
     * @param float $time
     *
     * @return float
     */
    private static function ms2s(int $time) : float
    {
        return $time / 1000;
    }

    private static function wait(float $from, float $for) : \generator
    {
        $cancel = false;

        while ((microtime(true) - $from) < $for && $cancel == false) {
            usleep(1);

            $cancel = yield;
        }
    }
}
