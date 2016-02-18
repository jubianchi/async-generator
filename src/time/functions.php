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

namespace jubianchi\async\time;

require_once __DIR__ . '/../time.php';

use jubianchi\async\time;

/**
 * @api
 *
 * @param int   $timeout
 * @param mixed $resolve
 *
 * @return \generator
 */
function delay(int $timeout, $resolve = null) : \generator
{
    return time::delay($timeout, $resolve);
}

/**
 * @api
 *
 * @param int $interval
 * @param $resolve
 *
 * @return \generator
 */
function throttle(int $interval, $resolve) : \generator
{
    return time::throttle($interval, $resolve);
}
