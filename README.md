mswoole-tcp
======
This is a simple framework, to make a easy tcp server;

First, you must intall yaf.so & swoole.so;

yaf : http://pecl.php.net/package/yaf

swoole : http://pecl.php.net/package/swoole


How to use it
------

After you install yaf.so & swoole.so;

    ```
    cd /swoole
    ```
There have three files;

    ```
    --- init.php // can start the server
    --- App.php // is core for this framework
    --- console.php // is a console for this server
    ````

Now we code this

    ```
    php console.php -s start
    // is support -s [start|reload|stop]
    ```

License
------

Apache License Version 2.0 see http://www.apache.org/licenses/LICENSE-2.0.html
