<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\runtime\{await, some};

function producer($prefix, $length) : \generator {
    $cancel = false;

    for ($i = 0; $i < $length && $cancel === false; $i++) {
        echo $prefix . '-' . $i . PHP_EOL;
        $cancel = (bool) yield;
    }

    if ($cancel === true) {
        echo $prefix . '-canceled' . PHP_EOL;
    }

    return $prefix . '-' . __LINE__;
}

var_dump(
    await(
        some(
            2,
            producer(__LINE__, 5),
            producer(__LINE__, 10),
            producer(__LINE__, 2)
        )
    )
);
