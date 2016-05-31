## Time (`jubianchi\async\time`)

### `jubianchi\async\time::delay(int $timeout, $resolve = null) : \generator`

`delay` take an integer timeout (miliseconds) as its first argument and an optionnal resolved value. It will return a 
generator which will delay the resolution of the value.

### `jubianchi\async\time::throttle(int $timeout, $resolve) : \generator`

`throttle` take an integer timeout (miliseconds) as its first argument and aresolved value. It will return a 
generator which will throttle the resolution of the value.


