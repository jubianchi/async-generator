# Documentation

## Introduction

PHP is a nice langage: it has been designed to be easy and fast but lacks concurrent programming capabilities. It is a
 **sequential** language.
 
Developers sometimes want to achieve **concurrent programming** with PHP and often end up using external technologies 
like workers. Those workers have many benefits but sometime they are too heavy to just handle simple tasks.

### Concurrent programming

When talking about concurrent programming it's useful to distinguish two methods:

* Parallel computing
* Concurrent computing

To sum-up the difference between those two paradigm, let's look at some diagram.

**Parellel computing** means two tasks will run in parallel without blocking each other:

```
    +- - - - - - - - - - -+
    |                     |
- - +                     + - - >
    |                     |
    +- - - - - - - - - - -+
```

**Concurrent programming** is a bit different: two tasks will race together and each time a task is paused, the main 
program will switch context and work on the other task: 

```
    +- - -       - -     -+
    |                     |
- - +                     + - - >
    |                     |
    +-     - - -     - - -+
```

### Async generators

Async generators try to solve this issue by **allowing developers to implement concurrent programming**. Thanks to the 
`yield` keyword, we can make PHP pause processing something and switch to another task.

The library provides some low-level functions grouped in logical namespaces to make things easier:

* [Runtime](runtime.md): this namespace provides generic functions to work with concurrency
* [Time](time.md): this namespace provides functions to work with time
* [Pipe](pipe.md): this namespace provides functions to work with pipes
* [Loop](loop.md): this namespace provides functions to make concurrent loops 
* [Stream](stream.md): this namespace provides functions to work with streams
* [Socket](socket.md): this namespace provides functions to work with sockets

