<?php

namespace jubianchi\async\runtime\tests\units;

use jubianchi\async\runtime\tests\funktion;
use jubianchi\async\runtime;
use function jubianchi\async\runtime\race;

class race extends funktion
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
            ->given($creator = function($limit, $value) {
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
                $creator = function($limit, $value) {
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
                ->object($generator = race($creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo($otherValue)
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
