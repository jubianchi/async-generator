## Runtime (`jubianchi\async\runtime`)

### `jubianchi\async\runtime::await(mixed $generatorOrValue) : mixed`

`await` takes a generator or a value as its single parameter.
 
When it gets a generator it will walk through it until its end. It will return the generator 
return value.

Given the following script where the `producer` function returns a generator:

```php
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
```

If we run this script we get the following output:

```
18-0
18-1
18-2
18-3
18-4
int(13)
```

The `await` call will walk until the end of the generator and resolve to the the generator return value.

### `jubianchi\async\runtime::all(...$generators) : \generator`

`all` takes one or more generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve to an array containing all the generators' return values.

Given the following script where the `producer` function returns a generator:

```php
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
```

If we run this script we get the following output:

```
19-0
20-0
19-1
20-1
19-2
19-3
19-4
array(2) {
  [0]=>
  string(5) "19-13"
  [1]=>
  string(5) "20-13"
}
```

As you can see, the `all` call will walk through all generators **alternately** and resolve to an array containing all the 
generators return values in the same order as the arguments passed to `all`.

**What's interesting here is that each `yield` is an opportunity for the runtime to switch task.**

### `jubianchi\async\runtime::race($first, $second, ...$generators) : \generator`

`race` takes several generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve with the value of the first finished generator.

Given the following script where the `producer` function returns a generator:

```php
<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use function jubianchi\async\runtime\{await, race};

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
        race(
            producer(__LINE__, 5),
            producer(__LINE__, 2)
        )
    )
);
```

If we run this script we get the following output:

```
25-0
26-0
25-1
26-1
25-2
25-canceled
string(5) "26-19"
```

`race` will handle the generators in the same way `all` does. The only difference is that it will immediately stop once
one of the generator is finished. It will then resolve to the this generator return value.

**What's interesting here is that each generator producer will receive (through the `yield`) a boolean indicating whether 
it should stop or not (here, we used the `$cancel` variable).**

### `jubianchi\async\runtime::some(int $howMany, ...$generators) : \generator`

`some` takes several generator as its last arguments and a number as its first argument. It will walk **concurrently** 
through each of the generators and return a generator as soon as the given number of generators are over.

Given the following script where the `producer` function returns a generator:

```php
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
```

If we run this script we get the following output:

```
26-0
27-0
28-0
26-1
27-1
28-1
26-2
27-2
26-3
27-3
26-4
27-4
27-canceled
array(2) {
  [0] =>
  string(5) "26-19"
  [2] =>
  string(5) "28-19"
}
```

`some` has a similar behavior than the `race` function. The only difference is that it will wait for a given number of 
generators to finish. It will return a generator which will resolve to an array containing all the generators' return 
values.
