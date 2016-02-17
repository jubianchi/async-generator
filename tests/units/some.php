<?php

namespace jubianchi\async\runtime\tests\units;

use jubianchi\async\runtime\tests\funktion;
use jubianchi\async\runtime;
use function jubianchi\async\runtime\some;

class some extends funktion
{
    /** @dataProvider valueDataProvider */
    public function testSomeValue($value)
    {
        $this
            ->object($generator = some(1, $value))->isInstanceOf(\generator::class)
            ->array(runtime\await($generator))->isIdenticalTo([$value])
            ->exception(function() use (& $expected) {
                    runtime\await(some($expected = rand(2, PHP_INT_MAX), uniqid()));
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
                ->object($generator = some(1, $value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([$value])
                ->object($generator = some(2, $value, $otherValue))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeGenerators($value, $otherValue = null)
    {
        $this
            ->given($creator = function($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->object($generator = some(1, $creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([$value])
                ->object($generator = some(2, $creator(3, $value), $creator(5, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testSomeGeneratorCreators($value, $otherValue = null)
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
                ->object($generator = some(1, $creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([1 => $otherValue])
                ->object($generator = some(2, $creator(5, $value), $creator(3, $otherValue)))->isInstanceOf(\generator::class)
                ->variable(runtime\await($generator))->isIdenticalTo([$value, $otherValue])
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
