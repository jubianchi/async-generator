<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\runtime\{await};

function producer($prefix, $length) : \generator {
    for ($i = 0; $i < $length; $i++) {
        echo $prefix . '-' . $i . PHP_EOL;
        yield;
    }

    return __LINE__;
}

var_dump(
    await(
        producer(__LINE__, 5)
    )
);
