---
layout: post
title: "PHP框架实战（二）：错误和异常的自动处理"
slug: flamework error auto handling
date: 2013-12-28 13:40:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

目标
----

实现错误和异常的自动捕获和处理。

获取代码
--------

```bash
git checkout v0.2
```

设计与实现
----------

使用set_error_handler()和set_exception_handler()两个函数注册错误和异常的处理方法，并在两个处理方法中先调用用户自定义的错误和异常处理逻辑，如果自定义逻辑不存在或者返回false，继续调用框架缺省的处理逻辑，输出错误信息到页面。

```php
<?php
class WebApplication {
    // ...

    /**
     * @var boolean Whether to enable error auto-handling, default to true 
     * @since 1.0
     */
    public $enableErrorHandling = true;
    /**
     * @var boolean Whether to enable exception auto-handling, default to true 
     * @since 1.0
     */
    public $enableExceptionHandling = true;
    /**
     * @var callable Error handler 
     * @since 1.0
     */
    public $errorHandler;
    /**
     * @var callable Exception handler 
     * @since 1.0
     */
    public $exceptionHandler;
    /**
     * @var boolean Whether to enable debug mode, default to false 
     * @since 1.0
     */
    public $debug = false;

    /**
     * @param string $config
     */
    public function __construct($config)
    {
        // ...

        $this->initErrorHandlers();
    }

    /**
     * Initialize auto-handling for errors and exceptions
     * @return void
     * @since 1.0
     */
    public function initErrorHandlers()
    {
        if ($this->enableErrorHandling == true)
            set_error_handler(array($this, 'handleError'), error_reporting());
        if ($this->enableExceptionHandling == true)
            set_exception_handler(array($this, 'handleException'));
    }
    
    /**
     * Handle errors
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @since 1.0
     */
    public function handleError($code, $message, $file, $line)
    {
        // prevent recursive errors
        restore_error_handler();
        restore_exception_handler();

        $msg = "Error $code: $message ($file:$line)";
        Flame::error($msg);

        // let errorHandler() return true to prevent displayError()
        if (is_callable($this->errorHandler) && call_user_func($this->errorHandler, $code, $message, $file, $line) !== true)
            $this->displayError($code, $message, $file, $line);

        exit(1);
    }
    
    /**
     * Handle exceptions
     * @param Exception $exception
     * @return void
     * @since 1.0
     */
    public function handleException($exception)
    {
        // prevent recursive errors
        restore_error_handler();
        restore_exception_handler();

        $msg = get_class($exception).': '.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine()."\n".$exception->getTraceAsString();
        Flame::error($msg);

        // let exceptionHandler() return true to prevent displayException()
        if (is_callable($this->exceptionHandler) && call_user_func($this->exceptionHandler, $exception) !== true)
            $this->displayException($exception);

        exit(1);
    }
    
    /**
     * Display error information
     * @param int $code
     * @param string $message
     * @param string $file
     * @param int $line
     * @return void
     * @since 1.0
     */
    public function displayError($code, $message, $file, $line)
    {
        if ($this->debug == true) {
            echo "<h1>Error $code</h1>";
            echo "<p>$message in ($file:$line)</p>";
            echo '<pre>';
            debug_print_backtrace();
            echo '</pre>';
        } else {
            echo "<h1>Error $code</h1>";
            echo "<p>$message</p>";
        }
    }
    
    /**
     * Display exception information
     * @param Exception $exception
     * @return void
     * @since 1.0
     */
    public function displayException($exception)
    {
        if ($this->debug == true) {
            echo '<h1>'.get_class($exception).'</h1>';
            echo '<p>'.$exception->getMessage().' ('.$exception->getFile().':'.$exception->getLine().')</p>';
            echo '<pre>'.$exception->getTraceAsString().'</pre>';
        } else {
            echo '<h1>'.get_class($exception).'</h1>';
            echo '<p>'.$exception->getMessage().'</p>';
        }
    }
    
    // ...
}
?>
```

“handleError()”和“handleException()”中先调用了“restore_error_handler()”和“restore_exception_handler()”，用于防止递归处理。

开发者可以在配置数组中指定自定义的错误和异常处理逻辑：

```php
<?php
return array(
    // ...
    'exceptionHandler' => function($exception) {
        var_dump($exception);
        return true;
    },
    'errorHandler' => array('MyClass', 'handleError'),
);
?>
```

“errorHandler”和“exceptionHandler”的值必须是一个callable类型，在这个callable结束时，如果不希望后续逻辑（_例如框架自己的错误、异常处理逻辑_）继续处理，就返回true，此时程序将会终止执行并退出。

Demo验证
--------

在WebApplication::run()中抛出一个异常或使用trigger_error()触发一个错误，可以看到均被拦截和处理。

总结
----

引入错误和异常的自动处理可以极大地简化代码。我们经常需要在AJAX请求出错时返回一个JSON字符串，并由客户端决定如何处理，这时就可以使用自定义的处理逻辑处理错误和异常，而Controller里只实现正常的逻辑即可，无需再有大量难看的try...catch块。
