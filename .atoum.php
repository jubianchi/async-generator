<?php

use mageekguy\atoum\reports;
use mageekguy\atoum\writers\std;
use mageekguy\atoum\reports\coverage;

$coverage = new coverage\html();
$coverage->addWriter(new \mageekguy\atoum\writers\std\out());
$coverage->setOutPutDirectory(__DIR__ . '/coverage');

$telemetry = new reports\telemetry();
$telemetry->addWriter(new std\out());
$telemetry->readProjectNameFromComposerJson(__DIR__ . DIRECTORY_SEPARATOR . 'composer.json');

$runner
    ->addExtension(new reports\extension($script))
    ->addReport($coverage)
    ->enableBranchesAndPathsCoverage()
    ->addReport($telemetry)
;

$script
    ->addTestsFromDirectory(__DIR__ . '/tests/units')
    ->addDefaultReport()
;
