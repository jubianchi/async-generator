# async-generator

This project is in early alpha and is more a like a POC than a real production-ready library.

# Documentation

## Runtime (`jubianchi\async\runtime`)

### `await`

`await` takes a generator or a value as its single parameter.
 
When it gets a generator it will walk through it until its end. It will return the generator 
return value.

`jubianchi\async\runtime::await(mixed $generatorOrValue) : mixed`

### `all`

`all` takes one or more generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve to an array containing all the generators' return values.

`jubianchi\async\runtime::all(...$generators) : \generator`

### `race`

`race` takes several generator as its arguments and will walk **concurrently** through each of them. It will return 
a generator which will resolve with the value of the first finished generator.

`jubianchi\async\runtime::race($first, $second, ...$generators) : \generator`

### `some`

`some` takes several generator as its last arguments and a number as its first argument. It will walk **concurrently** 
through each of the generators and return a generator as soon as the given number of generators are over.

`jubianchi\async\runtime::some(int $howMany, ...$generators) : \generator`

## Time (`jubianchi\async\time`)

### `delay`

`delay` take an integer timeout (miliseconds) as its first argument and an optionnal resolved value. It will return a 
generator which will delay the resolution of the value.

`jubianchi\async\time::delay(int $timeout, $resolve = null) : \generator`

### `throttle`

`throttle` take an integer timeout (miliseconds) as its first argument and aresolved value. It will return a 
generator which will throttle the resolution of the value.

`jubianchi\async\time::throttle(int $timeout, $resolve) : \generator`
