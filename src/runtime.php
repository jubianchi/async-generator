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

require_once __DIR__ . '/wrapper.php';

final class runtime
{
    /**
     * @param mixed $generatorOrValue
     *
     * @return mixed
     */
    public static function await($generatorOrValue)
    {
        switch (true) {
            case $generatorOrValue instanceof \generator:
                $generatorOrValue->current();

                while ($generatorOrValue->valid() === true) {
                    $generatorOrValue->next();
                }

                return self::await($generatorOrValue->getReturn());

            case is_callable($generatorOrValue):
                return self::await($generatorOrValue());

            case $generatorOrValue instanceof wrapper:
                return wrapper::unwrap($generatorOrValue);

            case is_array($generatorOrValue) === true:
                return array_map(
                    function ($generator) {
                        return self::await($generator);
                    },
                    $generatorOrValue
                );

            default:
                return $generatorOrValue;
        }
    }

    /**
     * @param \generator[] ...$generators
     *
     * @return \generator
     */
    public static function all(...$generators) : \generator
    {
        return self::some(count($generators), ...$generators);
    }

    /**
     * @param \generator|mixed $first
     * @param \generator|mixed $second
     * @param \generator[]  ...$generators
     *
     * @return \generator
     */
    public static function race($first, $second, ...$generators) : \generator
    {
        $cancel = false;
        $started = [];
        array_unshift($generators, $second);
        array_unshift($generators, $first);

        while (count($generators) > 0 && $cancel == false) {
            $generator = current($generators);
            $key = key($generators);

            switch (true) {
                case $generator instanceof \generator:
                    if (in_array($generator, $started, true)) {
                        $generator->next();
                    } else {
                        $generator->current();
                        $started[] = $generator;
                    }

                    if ($generator->valid() === false) {
                        unset($generators[$key]);

                        return self::await(self::cancel($generator->getReturn(), $generators));
                    }
                    break;

                default:
                    return $generator;
            }

            self::reset($generators);

            yield;
        }
    }

    /**
     * @param int $howMany
     * @param \generator[] ...$generators
     *
     * @return \generator
     */
    public static function some(int $howMany, ...$generators) : \generator
    {
        if ($howMany > count($generators)) {
            throw new \logicException(sprintf('Some expected at least %d generators', $howMany));
        }

        $results = [];
        $started = [];

        while (count($generators) > 0 && count($results) < $howMany) {
            $generator = current($generators);
            $key = key($generators);

            switch (true) {
                case $generator instanceof \generator:
                    if (in_array($generator, $started, true)) {
                        $generator->next();
                    } else {
                        $generator->current();
                        $started[] = $generator;
                    }


                    if ($generator->valid() === false) {
                        unset($generators[$key]);

                        $results[$key] = self::await($generator->getReturn());
                    }
                    break;

                default:
                    unset($generators[$key]);

                    $results[$key] = $generator;
                    break;
            }

            self::reset($generators);

            yield;
        }

        ksort($results);

        return self::cancel($results, $generators);
    }

    /**
     * @param \generator[] $generators
     *
     * @return \generator
     */
    public static function fork(array &$generators) : \generator
    {
        $cancel = false;

        while ($cancel == false) {
            $key = key($generators);
            $generator = current($generators);

            if ($generator instanceof \generator) {
                $generator->next();

                if ($generator->valid() === false) {
                    unset($generators[$key]);
                }
            } else {
                unset($generators[$key]);
            }

            self::reset($generators);

            $cancel = yield;
        }
    }

    private static function cancel($result, array $generators)
    {
        foreach ($generators as $generator) {
            if ($generator instanceof \generator && $generator->valid()) {
                $generator->send(true);
            }
        }

        return $result;
    }

    private static function reset(array &$generators)
    {
        $reset = next($generators);

        if ($reset === false) {
            $reset = reset($generators);
        }

        return $reset;
    }
}
