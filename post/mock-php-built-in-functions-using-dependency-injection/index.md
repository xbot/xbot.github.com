# 利用依赖注入Mock PHP的内建函数


## 简述

[上篇文章](/post/mock-php-built-in-functions-using-namespace/)讨论了如何利用命名空间实现对PHP内建函数的mocking，本文介绍另一种实现方法——依赖注入。

出于编写可测试代码的需要，依赖注入是经常使用的一种技术。通过把代码中依赖的其它数据获取服务提取出来、和原有逻辑解耦，提高代码的可测试性。只需mock这些依赖并注入到测试对象中即可。

## 实现

### 对原有代码的重构

先把原有代码用依赖注入的方式重构（*为突出重点，省略了和上篇文章中重复的部分*）：

```php

// ...

use Ox3f\LaravelUtils\Services\Builtins;

/**
 * Class Log
 * @author donie
 */
class Log
{
    // ...
    
    private $builtins;        // Builtin functions

    private function __construct() {
        $this->builtins = new Builtins();
        
        // ...
    }

    // ...

    public static function inject($key, $service)
    {
        self::getInstance()->$key = $service;
    }

    /**
     * Parse the call stack
     *
     * @return void
     */
    private function parseCallStack() {
        $traceInfo = $this->builtins->debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT|DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        
        // ...
        
    }
    
    // ...
    
}
```

`Builtins`类的实现：

```php
<?php
namespace Ox3f\LaravelUtils\Services;

/**
 * Class Builtins
 * @author donie
 */
class Builtins
{
    public function debug_backtrace()
    {
        return call_user_func_array('debug_backtrace', func_get_args());
    }
    
}
```

把`debug_backtrace()`封装进了`Builtins`类，并在测试对象中通过这个类调用内建函数。`inject()`用于注入依赖，这样可以在测试类中把mock注入到测试对象中。

### 测试类的实现

代码如下：

```php
<?php
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Ox3f\LaravelUtils\Log\Log;
use \Mockery as m;

class LogTest extends TestCase
{
    public function setUp()
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object)['name' => 'jim',]);
    }

    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers Ox3f\LaravelUtils\Log\Log::saveInput
     * @covers Ox3f\LaravelUtils\Log\Log::saveOutput
     * @covers Ox3f\LaravelUtils\Log\Log::parseCallStack
     * @covers Ox3f\LaravelUtils\Log\Log::getInstance
     * @covers Ox3f\LaravelUtils\Log\Log::__construct
     * @covers Ox3f\LaravelUtils\Log\Log::__callStatic
     */
    public function testAll()
    {
        $mock = m::mock('Ox3f\LaravelUtils\Services\Builtins');
        $mock->shouldReceive('debug_backtrace')
            ->andReturnUsing(function() use ($mock) {
                if ($mock->calledInController) {
                    return json_decode('[{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/xbot\/laravel-utils\/src\/Log\/Log.php","line":85,"function":"parseCallStack","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Api\/V1\/Controllers\/WorkController.php","line":29,"function":"saveInput","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","type":"::"},{"function":"save","class":"App\\\Api\\\V1\\\Controllers\\\WorkController","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php","line":55,"function":"call_user_func_array"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/ControllerDispatcher.php","line":44,"function":"callAction","class":"Illuminate\\\Routing\\\Controller","object":{},"type":"->"}]', true);
                } else {
                    return json_decode('[{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/xbot\/laravel-utils\/src\/Log\/Log.php","line":85,"function":"parseCallStack","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Notation.php","line":21,"function":"saveInput","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","type":"::"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Api\/V1\/Controllers\/NotationController.php","line":32,"function":"incrNo","class":"App\\\Notation","type":"::"},{"function":"save","class":"App\\\Api\\\V1\\\Controllers\\\NotationController","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php","line":55,"function":"call_user_func_array"}]', true);
                }
            });
        Log::inject('builtins', $mock);

        // test being called in a plain method
        $mock->calledInController = false;
        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | App\Notation::incrNo | Input:1');

        Log::saveInput(1);

        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | App\Notation::incrNo | Output:2');

        Log::saveOutput(2);

        // test being called in a controller action
        $mock->calledInController = true;
        Request::shouldReceive('path')
            ->once()
            ->andReturn('api/user');
        Request::shouldReceive('except')
            ->once()
            ->with('_url')
            ->andReturn(['id' => 18,]);
        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | api/user | Input:{"id":18}');

        Log::saveInput();

        Request::shouldReceive('path')
            ->once()
            ->andReturn('api/user');
        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | api/user | Output:2');

        Log::saveOutput(2);

        Request::shouldReceive('path')
            ->once()
            ->andReturn('api/user');
        LaravelLog::shouldReceive('error')
            ->once()
            ->with('jim | api/user | this is an error');

        Log::error('this is an error');
        
        $this->assertEquals(0, 0);
    }
}
```

`Builtins`是个普通类，很容易mock。


