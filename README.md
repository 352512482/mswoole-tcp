Swoole
======
[![Build Status](https://api.travis-ci.org/swoole/swoole-src.svg)](https://travis-ci.org/swoole/swoole-src)
[![License](https://img.shields.io/badge/license-apache2-blue.svg)](LICENSE)
[![Join the chat at https://gitter.im/swoole/swoole-src](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/swoole/swoole-src?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Swoole is an event-driven asynchronous & concurrent networking communication framework with high performance written only in C for PHP.

__Document__: <https://rawgit.com/tchiotludo/swoole-ide-helper/english/docs/index.html>

__IDE Helper__: <https://github.com/swoole/ide-helper>

__中文文档__: <http://wiki.swoole.com/>

__IRC__:  <https://gitter.im/swoole/swoole-src>


event-based
------

The network layer in Swoole is event-based and takes full advantage of the underlaying epoll/kqueue implementation, making it really easy to serve thousands of connections.

coroutine
----------------
[Swoole 2.0](Version2.md) support the  built-in coroutine, you can use fully synchronized code to implement asynchronous program. 
PHP code without any additional keywords, the underlying automatic coroutine-scheduling.

concurrent
------

On the request processing part, Swoole uses a multi-process model. Every process works as a worker. All business logic is executed in workers, synchronously.

With the synchronous logic execution, you can easily write large and robust applications and take advantage of almost all libraries available to the PHP community.

in-memory
------

Unlike traditional apache/php-fpm stuff, the memory allocated in Swoole will not be free'd after a request, which can improve performance a lot.


## Why Swoole?

Traditional PHP applications almost always run behind Apache/Nginx, without much control of the request. This brings several limitations:

1. All memory will be freed after request. All PHP code needs be re-compiled on every request. Even with opcache enabled, all opcode still needs to be re-executed.
2. It is almost impossible to implement long connections and connections pooling techniques.
3. Implementing asynchronous tasks requires 3rd party queue servers, such as rabbitmq and beanstalkd.
4. Implementing realtime applications such as chatting server requires 3rd party languages, nodejs for example.

This why Swoole appeared. Swoole extends the use cases of PHP, and brings all these possibilities to the PHP world. 
By using Swoole, you can build enhanced web applications with more control, real-time chatting servers, etc more easily.

## Requirements

* PHP 5.3.10 or later
* Linux, OS X and basic Windows support (Thanks to cygwin)
* GCC 4.4 or later

## Installation

1. Install via pecl
    
    ```
    pecl install swoole
    ```

2. Install from source

    ```
    sudo apt-get install php5-dev
    git clone https://github.com/swoole/swoole-src.git
    cd swoole-src
    phpize
    ./configure
    make && make install
    ```

Refer [API Reference](http://wiki.swoole.com/wiki/page/3.html) for more detail information of these functions.

## API Reference

* [中文](http://wiki.swoole.com/) 
* [English](https://rawgit.com/tchiotludo/swoole-ide-helper/english/docs/index.html)

## Related Projects

* [SwooleFramework](https://github.com/swoole/framework) Web framework powered by Swoole

## Contribution

Your contribution to Swoole development is very welcome!

You may contribute in the following ways:

* [Repost issues and feedback](https://github.com/swoole/swoole-src/issues)
* Submit fixes, features via Pull Request
* Write/polish documentation

## License

Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html
