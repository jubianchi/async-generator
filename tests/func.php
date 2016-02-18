<?php

/*
 * This file is part of the async generator runtime project.
 *
 * (c) Julien Bianchi <contact@jubianchi.fr>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace jubianchi\async\runtime\tests;

use mageekguy\atoum;

abstract class func extends atoum\test
{
    public function getTestedClassName()
    {
        return 'stdClass';
    }

    public function getTestedClassNamespace()
    {
        return 'jubianchi\\async';
    }
}
