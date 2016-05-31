<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\runtime\{await, all};

function producer($prefix, $length) : \generator {
    for ($i = 0; $i < $length; $i++) {
        echo $prefix . '-' . $i . PHP_EOL;
        yield;
    }

    return $prefix . '-' . __LINE__;
}

var_dump(
    await(
        all(
            producer(__LINE__, 5),
            producer(__LINE__, 2)
        )
    )
);
