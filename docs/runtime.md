## Runtime (`jubianchi\async\runtime`)

### `jubianchi\async\runtime::await(mixed $generatorOrValue) : mixed`

`await` takes a generator or a value as its single parameter.
 
When it gets a generator it will walk through it until its end. It will return the generator 
return value.

### `jubianchi\async\runtime::all(...$generators) : \generator`

`all` takes one or more generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve to an array containing all the generators' return values.

### `jubianchi\async\runtime::race($first, $second, ...$generators) : \generator`

`race` takes several generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve with the value of the first finished generator.

### `jubianchi\async\runtime::some(int $howMany, ...$generators) : \generator`

`some` takes several generator as its last arguments and a number as its first argument. It will walk **concurrently** 
through each of the generators and return a generator as soon as the given number of generators are over.
