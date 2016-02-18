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

use function jubianchi\async\runtime\await;
use jubianchi\async\runtime;
use jubianchi\async\runtime\tests\func;

class await extends func
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
            ->given($creator = function ($limit, $value) {
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
                ->variable(await($creator(3, $value)))->isIdenticalTo($value)
        ;
    }

    /** @dataProvider valueDataProvider */
    public function testAwaitWrappedGeneratorCreator($value)
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
                ->object(await(runtime\wrap($creator(3, $value))))->isInstanceOf(\generator::class)
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
