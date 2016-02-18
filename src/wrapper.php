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

final class wrapper
{
    private $wrapped;

    private function __construct(\generator $value)
    {
        $this->wrapped = $value;
    }

    public static function unwrap(wrapper $wrapper) : \generator
    {
        return $wrapper->wrapped;
    }

    public static function wrap(\generator $value)
    {
        return new self($value);
    }
}
