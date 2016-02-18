<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jubianchi\async\tests\units\runtime;

use function jubianchi\async\runtime\race;
use jubianchi\async\runtime;
use jubianchi\async\runtime\tests\func;

class race extends func
{
    /** @dataProvider valueDataProvider */
    public function testRaceValue($value)
    {
        $this
            ->object($generator = race($value))->isInstanceOf(\generator::class)
            ->variable(runtime\await($generator))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceValues($value, $otherValue = null)
    {
        $this
            ->given($otherValue = $otherValue ?? $value)
            ->then
                ->object($generator = race($value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceGenerator($value)
    {
        $this
            ->given($creator = function ($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->object($generator = race($creator(3, $value)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceGenerators($value, $otherValue = null)
    {
        $this
            ->given(
                $otherValue = $otherValue ?? $value,
                $creator = function ($limit, $value) {
                    while ($limit-- > 0) {
                        yield;
                    }

                    return $value;
                }
            )
            ->then
                ->object($generator = race($creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($otherValue)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceGeneratorCreators($value, $otherValue = null)
    {
        $this
            ->given(
                $otherValue = $otherValue ?? $value,
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
                ->object($generator = race($creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($otherValue)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceWrappedGeneratorCreators($value, $otherValue = null)
    {
        $this
            ->given(
                $otherValue = $otherValue ?? $value,
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
                ->object($generator = race(runtime\wrap($creator(5, $value)), runtime\wrap($creator(3, $otherValue))))->isInstanceOf(\generator::class)
                ->object(runtime\await($generator))->isInstanceOf(\generator::class)
                ->variable(runtime\await(runtime\await($generator)))->isIdenticalTo($value)
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
