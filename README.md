# async-generator

**This project is in early alpha and is more a like a POC than a real production-ready library.**

## Requirements

The [async-generator](https://github.com/jubianchi/async-generator) library requires PHP `^7.0.3`(i.e PHP `>= 7.0.3 && < 8.0.0`)

## Install 

Use [Composer](https://getcomposer.org/) to install this library into your project:

```json
{
    "require": {
        "jubianchi/async-generator": "@stable"
    }
}
```

Then run `composer up jubianchi/async-generator` and everything should be ready.

If you don't want to manually edit your `composer.json` file, simply run `composer require jubianchi/async-generator`
and you should be ready.

## Testing

Once everything is installed, create a simple PHP file: 

```php
<?php

//index.php

require_once __DIR__ . '/vendor/autoload.php';

use function jubianchi\async\runtime\{await, all};
use function jubianchi\async\time\{delay};

await(
    all(
        delay(5000, function() { echo 'World!'; }),
        delay(2000, function() { echo 'Hello '; })
    )
);
```

Run this script using `time php index.php`: if everything is OK, you should see the word `Hello` after 2 seconds and `World!` 
after 5 seconds:

```
Hello World!

real    0m5.029s
user    0m4.234s
sys     0m0.317s
```

Notice the whole script took about 5 seconds to complete when the total delay is 7 seconds: this is all the magic behind 
this library. It allows you to write **concurrent** tasks with PHP!
