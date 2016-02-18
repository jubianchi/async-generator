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

namespace jubianchi\async\process;

function spawn(string $cmd, callable $output = null, callable $error = null) : \generator
{
    $output = $output ?? function () {};
    $error = $error ?? function () {};

    $pipes = [];
    $proc = proc_open(
        $cmd,
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes
    );
    $cancel = false;

    while ($cancel == false && proc_get_status($proc)['running'] === true) {
        $read = ['output' => $pipes[1], 'error' => $pipes[2]];
        $write = $except = null;

        if (stream_select($read, $write, $except, 0, 1) > 0) {
            foreach ($read as $key => $stream) {
                $buffer = stream_get_contents($stream);

                if ($buffer !== '') {
                    ${$key}($buffer);
                }
            }
        }

        $cancel = yield;
    }

    if ($cancel === true) {
        proc_terminate($proc, SIGKILL);
    } else {
        $read = ['output' => $pipes[1], 'error' => $pipes[2]];
        $write = $except = null;

        if (stream_select($read, $write, $except, 0, 1) > 0) {
            foreach ($read as $key => $stream) {
                $buffer = stream_get_contents($stream);

                if ($buffer !== '') {
                    ${$key}($buffer);
                }
            }
        }
    }

    $status = proc_get_status($proc);

    proc_close($proc);

    return $status;
}

function passthru(string $cmd) : \generator
{
    $pipes = [];
    $proc = proc_open(
        $cmd,
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes
    );
    $cancel = false;
    $contents = '';

    while ($cancel == false && proc_get_status($proc)['running'] === true) {
        $out = [$pipes[1]];
        $err = [$pipes[2]];
        $write = $except = null;

        if (stream_select($out, $write, $except, 0, 1) > 0) {
            $buffer = stream_get_contents($out[0]);

            if ($buffer !== '') {
                $contents .= $buffer;
            }
        }

        if (stream_select($err, $write, $except, 0, 1) > 0) {
            $buffer = stream_get_contents($err[0]);

            if ($buffer !== '') {
                $contents .= $buffer;
            }
        }

        $cancel = yield;
    }

    if ($cancel === true) {
        proc_terminate($proc);
    } else {
        $out = [$pipes[1]];
        $err = [$pipes[2]];
        $write = $except = null;

        if (stream_select($out, $write, $except, 0, 1) > 0) {
            $buffer = stream_get_contents($out[0]);

            if ($buffer !== '') {
                $contents .= $buffer;
            }
        }

        if (stream_select($err, $write, $except, 0, 1) > 0) {
            $buffer = stream_get_contents($err[0]);

            if ($buffer !== '') {
                $contents .= $buffer;
            }
        }
    }

    proc_close($proc);

    return $contents;
}
