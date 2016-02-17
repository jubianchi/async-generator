<?php

namespace jubianchi\async\runtime\tests\units;

use jubianchi\async\runtime\tests\funktion;
use jubianchi\async\runtime;
use function jubianchi\async\runtime\all;

class all extends funktion
{
    /** @dataProvider valueDataProvider */
    public function testAllValue($value)
    {
        $this
            ->object($generator = all($value))->isInstanceOf(\generator::class)
            ->array(runtime\await($generator))->isIdenticalTo([$value])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllValues($value, $otherValue = null)
    {
        $this
            ->given($otherValue = $otherValue ?? $value)
            ->then
                ->object($generator = all($value, $otherValue))->isInstanceOf(\generator::class)
                ->array(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGenerator($value)
    {
        $this
            ->given($creator = function($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->object($generator = all($creator(3, $value)))->isInstanceOf(\generator::class)
                ->array(runtime\await($generator))->isIdenticalTo([$value])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGenerators($value, $otherValue = null)
    {
        $this
            ->given(
                $otherValue = $otherValue ?? $value,
                $creator = function($limit, $value) {
                    while ($limit-- > 0) {
                        yield;
                    }

                    return $value;
                }
            )
            ->then
                ->object($generator = all($creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->array(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAllGeneratorCreators($value, $otherValue = null)
    {
        $this
            ->given(
                $otherValue = $otherValue ?? $value,
                $creator = function($limit, $value) {
                    while ($limit-- > 0) {
                        yield;
                    }

                    return (function() use ($value) {
                        yield;

                        return $value;
                    })();
                }
            )
            ->then
                ->object($generator = all($creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->array(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
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
            [new \stdClass, new \stdClass]
        ];
    }
}
