<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jubianchi\async\tests\units;

use jubianchi\async\runtime as testedClass;
use mageekguy\atoum;

class runtime extends atoum\test
{
    /** @dataProvider valueDataProvider */
    public function testAwaitValue($value)
    {
        $this->variable(testedClass::await($value))->isIdenticalTo($value);
    }

    /** @dataProvider valueDataProvider */
    public function testAwaitGenerator($value)
    {
        $this
            ->given($creator = function ($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->variable(testedClass::await($creator(3, $value)))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAwaitGeneratorCreator($value)
    {
        $this
            ->given($creator = function ($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return (function () use ($value) {
                    yield;

                    return $value;
                })();
            })
            ->then
                ->variable(testedClass::await($creator(3, $value)))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllValue($value)
    {
        $this
            ->object($generator = testedClass::all($value))->isInstanceOf(\generator::class)
            ->array(testedClass::await($generator))->isIdenticalTo([$value])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllValues($value, $otherValue = null)
    {
        $this
            ->given($otherValue = $otherValue ?? $value)
            ->then
                ->object($generator = testedClass::all($value, $otherValue))->isInstanceOf(\generator::class)
                ->array(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGenerator($value)
    {
        $this
            ->given($creator = function ($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->object($generator = testedClass::all($creator(3, $value)))->isInstanceOf(\generator::class)
                ->array(testedClass::await($generator))->isIdenticalTo([$value])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGenerators($value, $otherValue = null)
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
                ->object($generator = testedClass::all($creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->array(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGeneratorCreators($value, $otherValue = null)
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
                ->object($generator = testedClass::all($creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->array(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceValue($value)
    {
        $this
            ->object($generator = testedClass::race($value))->isInstanceOf(\generator::class)
            ->variable(testedClass::await($generator))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testRaceValues($value, $otherValue = null)
    {
        $this
            ->given($otherValue = $otherValue ?? $value)
            ->then
                ->object($generator = testedClass::race($value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo($value)
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
                ->object($generator = testedClass::race($creator(3, $value)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo($value)
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
                ->object($generator = testedClass::race($creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo($otherValue)
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
                ->object($generator = testedClass::race($creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo($otherValue)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeValue($value)
    {
        $this
            ->object($generator = testedClass::some(1, $value))->isInstanceOf(\generator::class)
            ->array(testedClass::await($generator))->isIdenticalTo([$value])
            ->exception(function () use (&$expected) {
                testedClass::await(testedClass::some($expected = rand(2, PHP_INT_MAX), uniqid()));
                }
            )
                ->isInstanceOf(\logicException::class)
                ->hasMessage(sprintf('Some expected at least %d generators', $expected))
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeValues($value, $otherValue = null)
    {
        $this
            ->given($otherValue = $otherValue ?? $value)
            ->then
                ->object($generator = testedClass::some(1, $value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([$value])
                ->object($generator = testedClass::some(2, $value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeGenerators($value, $otherValue = null)
    {
        $this
            ->given($creator = function ($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->object($generator = testedClass::some(1, $creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([$value])
                ->object($generator = testedClass::some(2, $creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeGeneratorCreators($value, $otherValue = null)
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
                ->object($generator = testedClass::some(1, $creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([1 => $otherValue])
                ->object($generator = testedClass::some(2, $creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(testedClass::await($generator))->isIdenticalTo([$value, $otherValue])
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
