<?php

namespace jubianchi\async\runtime\tests\units;

use jubianchi\async\runtime\tests\funktion;
use function jubianchi\async\runtime\await;

class await extends funktion
{
    /** @dataProvider valueDataProvider */
    public function testAwaitValue($value)
    {
        $this->variable(await($value))->isIdenticalTo($value);
    }

    /** @dataProvider valueDataProvider */
    public function testAwaitGenerator($value)
    {
        $this
            ->given($creator = function($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return $value;
            })
            ->then
                ->variable(await($creator(3, $value)))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAwaitGeneratorCreator($value)
    {
        $this
            ->given($creator = function($limit, $value) {
                while ($limit-- > 0) {
                    yield;
                }

                return (function() use ($value) {
                    yield;

                    return $value;
                })();
            })
            ->then
                ->variable(await($creator(3, $value)))->isIdenticalTo($value)
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
