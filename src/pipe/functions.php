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

namespace jubianchi\async\pipe;

require_once __DIR__ . '/../pipe.php';

use jubianchi\async\pipe;

function make() : pipe
{
    return new pipe();
}
