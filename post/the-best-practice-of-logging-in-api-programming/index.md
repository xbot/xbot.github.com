# 接口编程中记录日志的最佳实践


## 解决什么问题

* 快速定位日志
* 降低记录成本
* 提高代码可读性

日志框架是项目开始阶段应该最先搭建好的内容之一，有助于极大地节约以后解决问题的时间和成本。但这也是最让我头疼的问题之一，因为记日志不光包括用什么记、怎么记，也包括记什么内容，这恰恰是最容易被忽略的问题。

一条好的日志需要做到能让问题的跟踪者快速定位它在程序中的位置且包含关键数据。工作中不乏这样的团队成员，在移交接口问题的时候没有主动提供关键信息的意识，所谓关键信息，是指像问题发生的环境、接口名、传递的实参和返回结果这样的内容，使得面向契约编程本来是很好的开发模式，却得不到最好的应用。这时我们可以从自己接口的日志中得到一些弥补。

但是日志的记录不应该占用很大的代码量，一来降低了代码的可读性，二来会耗费太多的时间精力。

本文目的在于讨论一种日志记录的最佳实践方式，使得可以兼顾以上这些问题。

## 实践

这里针对Laravel实现一个日志工具类，实现以下功能：

* 兼容Laravel自己的日志系统
* 自动记录关键的业务数据
* 自动记录日志所属的接口名
* 自动记录接口的实参
* 记录接口的返回值
* 生成简洁规范的日志内容

代码：

```php
<?php

namespace Ox3f\LaravelUtils\Log;

use Illuminate\Support\Facades\Log as LaravelLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class Log
 * @author donie
 */
class Log
{
    private static $instance;

    private $id;              // Identity of the log, username by default.
    private $referer;         // Request path for RESTful APIs, method name for ordinary class methods.
    private $isHttp;          // True for RESTful APIs, otherwise, false.
    private $callStackParsed; // Whether call stack has been parsed.

    private function __construct() {
        $user = Auth::user();
        $this->id = !empty($user->name) ? $user->name : 'anonymous';
    }
    private function __clone() {}

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Parse the call stack
     *
     * @return void
     */
    private function parseCallStack() {
        $traceInfo = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $this->referer = '';
        $this->isHttp = false;
        foreach ($traceInfo as $callInfo) {
            if ($callInfo['class'] != __CLASS__) {
                if (preg_match('/Controller$/', $callInfo['class'])) {
                    $this->referer = Request::path();
                    $this->isHttp = true;
                } else {
                    $this->referer = $callInfo['class'].$callInfo['type'].$callInfo['function'];
                }
                break;
            }
        }
        $this->callStackParsed = true;
    }

    /**
     * Wrapper of the laravel log facade
     *
     * @return void
     */
    public static function __callStatic($name, $args)
    {
        if (!self::getInstance()->callStackParsed)
            self::getInstance()->parseCallStack();

        $id      = self::getInstance()->id;
        $referer = self::getInstance()->referer;
        $msg     = !empty($args) ? $args[0] : '';
        LaravelLog::$name("{$id} | {$referer} | {$msg}");

        self::getInstance()->callStackParsed = false;
    }
    
    /**
     * Save parameters of the request or arguments of the method to log at debug level
     *
     * @param mixed $args Empty for HTTP calls, needed for ordinary class methods
     * @return void
     */
    public static function saveInput($args=null)
    {
        self::getInstance()->parseCallStack();
        if (self::getInstance()->isHttp) $args = Request::except('_url');
        self::debug('Input:'.json_encode($args));
    }
    
    /**
     * Save the output to log at debug level
     *
     * @param mixed $result Result to be saved
     * @return void
     */
    public static function saveOutput($result)
    {
        self::getInstance()->parseCallStack();
        self::debug('Output:'.json_encode($result));
    }
}
```

这是一个单例类，核心在于`parseCallStack()`方法，通过`debug_backtrace()`函数获取日志所在的接口，对于REST接口，得到HTTP请求的路径，对于接口类的方法，得到包含类名的接口名。此外，日志中还会记录当前的用户名，方便定位和复现问题。

`saveInput()`和`saveOutput()`是在此基础上封装的两个高级方法，用于记录接口的输入和输出，对于界定问题是否出在自己的接口或复现问题都有很重要的作用。对于REST接口，`saveInput()`可以自动获取请求中的参数，而对于接口类的方法，出于性能和内存占用考虑，没有允许`debug_backtrace()`返回参数信息，需要用户手动指定要记录的数据。

### 使用方法

#### 安装
```bash
composer require xbot/laravel-utils
```
#### 使用
```php
use Ox3f\LaravelUtils\Log\Log;

Log::saveInput();                   // REST接口中自动保存请求数据
Log::saveInput(func_get_args());    // 接口类的方法中保存实参
Log::error('This is an error.');    // 记录一条错误日志
Log::saveOutput($result);           // 保存接口返回值
```
#### 日志示例
```
[2017-04-25 06:46:11] local.DEBUG: donie | users/groups/33 | Input:{"check":"1"}
[2017-04-25 06:46:11] local.ERROR: donie | users/groups/33 | This is an error.
```

## 待讨论的问题

最佳实践需要持续改进，以下问题有待讨论：

### 关键业务数据中是否应该包含Request ID？
是否有必要对每次请求生成一个ID？这样可以很简单地过滤出一次请求中所有的日志。


