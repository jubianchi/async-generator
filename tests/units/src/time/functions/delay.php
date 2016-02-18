<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jubianchi\async\tests\units\time;

use function jubianchi\async\time\delay;
use jubianchi\async\runtime;
use jubianchi\async\runtime\tests\func;

class delay extends func
{
    /** @dataProvider valueDataProvider */
    public function testDelayValue($value)
    {
        static $index = 0;

        $this
            ->given(
                $timeout = 500,
                $current = 0,
                $this->function->microtime = function () use ($timeout, &$current) {
                    $value = $current;

                    $current += ($timeout / 1000);

                    return $value;
                }
            )
            ->then
                ->object($generator = delay($timeout, $value))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($value)
                ->function('microtime')
                    ->wasCalledWithArguments(true)->exactly(++$index * 2)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testDelayGenerator($value)
    {
        static $index = 0;

        $this
            ->given(
                $timeout = 500,
                $current = 0,
                $this->function->microtime = function () use ($timeout, &$current) {
                    $value = $current;

                    $current += ($timeout / 1000);

                    return $value;
                },
                $creator = function ($limit, $value) {
                    while ($limit-- > 0) {
                        yield;
                    }

                    return (function () use ($value) {
                        yield;

                        return $value;
                    })();
                }
            )
            ->then
                ->object($generator = delay($timeout, $creator(3, $value)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($value)
                ->function('microtime')
                    ->wasCalledWithArguments(true)->exactly(++$index * 2)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testDelayGeneratorCreator($value)
    {
        static $index = 0;

        $this
            ->given(
                $timeout = 500,
                $current = 0,
                $this->function->microtime = function () use ($timeout, &$current) {
                    $value = $current;

                    $current += ($timeout / 1000);

                    return $value;
                },
                $creator = function ($limit, $value) {
                    while ($limit-- > 0) {
                        yield;
                    }

                    return (function () use ($value) {
                        yield;

                        return $value;
                    })();
                }
            )
            ->then
                ->object($generator = delay($timeout, $creator(3, $value)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($value)
                ->function('microtime')
                    ->wasCalledWithArguments(true)->exactly(++$index * 2)
        ;
    }

    protected function valueDataProvider()
    {
        return [
            [rand(0, PHP_INT_MAX), rand(0, PHP_INT_MAX)],
            [1 / 3, 7 / 5],
            [uniqid(), uniqid()],
            [false, true],
            [true, false],
            [null],
            [range(0, 2), range(2, 4)],
            [new \stdClass(), new \stdClass()],
        ];
    }
}
