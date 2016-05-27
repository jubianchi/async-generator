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

namespace jubianchi\async\runtime;

require_once __DIR__ . '/../runtime.php';
require_once __DIR__ . '/../wrapper.php';

use jubianchi\async\runtime;
use jubianchi\async\wrapper;

function await($generatorOrValue)
{
    return runtime::await($generatorOrValue);
}

function all(...$generators) : \generator
{
    return runtime::all(...$generators);
}

function race($first, $second, ...$generators) : \generator
{
    return runtime::race($first, $second, ...$generators);
}

function some(int $howMany, ...$generators) : \generator
{
    return runtime::some($howMany, ...$generators);
}

function fork(&$generators) : \generator
{
    return runtime::fork($generators);
}

function wrap($value) : wrapper
{
    return wrapper::wrap($value);
}

function unwrap(wrapper $wrapper)
{
    return wrapper::unwrap($wrapper);
}
