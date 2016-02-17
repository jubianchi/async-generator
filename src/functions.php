<?php

namespace jubianchi\async\runtime;

function await($generator) {
    switch (true) {
        case $generator instanceof \generator:
            while ($generator->valid() === true) {
                $generator->next();
            }

            return await($generator->getReturn());

        default:
            return $generator;
    }
}

function all(...$generators) {
    $results = [];

    while(count($generators) > 0) {
        $generator = current($generators);
        $key = key($generators);

        switch (true) {
            case $generator instanceof \generator:
                $generator->next();

                if ($generator->valid() === false) {
                    unset($generators[$key]);

                    $results[$key] = await($generator->getReturn());
                }
                break;

            default:
                unset($generators[$key]);

                $results[$key] = $generator;
                break;
        }

        if (next($generators) === false) {
            reset($generators);
        };

        yield;
    }

    ksort($results);

    return $results;
}

function race(...$generators) {
    while(count($generators) > 0) {
        $generator = current($generators);
        $key = key($generators);

        switch (true) {
            case $generator instanceof \generator:
                $generator->next();

                if ($generator->valid() === false) {
                    unset($generators[$key]);

                    return await($generator->getReturn());
                }
                break;

            default:
                return $generator;
        }

        if (next($generators) === false) {
            reset($generators);
        }

        yield;
    }

    return null;
}

function some($howMany, ...$generators) {
    if ($howMany > count($generators)) {
        throw new \logicException(sprintf('Some expected at least %d generators', $howMany));
    }

    $results = [];

    while(count($generators) > 0 && count($results) < $howMany) {
        $generator = current($generators);
        $key = key($generators);

        switch (true) {
            case $generator instanceof \generator:
                $generator->next();

                if ($generator->valid() === false) {
                    unset($generators[$key]);

                    $results[$key] = await($generator->getReturn());

                    reset($generators);
                }
                break;

            default:
                unset($generators[$key]);

                $results[$key] = $generator;
                break;
        }

        if (next($generators) === false) {
            reset($generators);
        }

        yield;
    }

    ksort($results);

    return $results;
}

function delay($timeout, $resolve) {
    $timeout = $timeout / 1000;
    $start = microtime(true);

    while((($current = microtime(true)) - $start) < $timeout) {
        usleep(1);

        yield;
    }

    return await($resolve);
}
