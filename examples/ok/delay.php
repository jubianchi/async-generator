<?php

//index.php

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\runtime\{await, all};
use function jubianchi\async\time\{delay};

$start = microtime(true);

await(
    all(
        delay(5000, function() { echo 'World!'; }),
        delay(2000, function() { echo 'Hello'; })
    )
);

echo PHP_EOL . 'Time spent: ' . ($with = microtime(true) - $start) . PHP_EOL;
