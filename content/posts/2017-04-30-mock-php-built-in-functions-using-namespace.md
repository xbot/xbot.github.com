---
title: 利用命名空间Mock PHP的内建函数
slug: mock php built in functions using namespace
date: 2017-04-30 09:06:17
categories:
- 计算机
tags:
- php
- 编程
- 测试
---

## 简述

Mock PHP的内建函数一般有两种方法：命名空间法和依赖注入法。

命名空间法是利用PHP优先使用同命名空间内函数的特性，在测试对象的命名空间内重载内建函数来实现。前提是内建函数在被调用时没有使用命名空间，例如：`\debug_backtrace()`是不能使用本方法的。

上篇文章[《接口编程中记录日志的最佳实践》](/post/the-best-practice-of-logging-in-api-programming/)中实现的日志类中，核心部分调用了`debug_backtrace()`函数获取方法调用堆栈。下面讨论下如何利用命名空间法实现对此函数的mock。

## 实现

先看代码：

```php
<?php
namespace Ox3f\LaravelUtils\Log;

use PHPUnit\Framework\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Log as LaravelLog;
use Ox3f\LaravelUtils\Log\Log;

$calledInController = false;

function debug_backtrace() {
    global $calledInController;
    if ($calledInController) {
        return json_decode('[{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/xbot\/laravel-utils\/src\/Log\/Log.php","line":85,"function":"parseCallStack","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Api\/V1\/Controllers\/WorkController.php","line":29,"function":"saveInput","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","type":"::"},{"function":"save","class":"App\\\Api\\\V1\\\Controllers\\\WorkController","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php","line":55,"function":"call_user_func_array"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/ControllerDispatcher.php","line":44,"function":"callAction","class":"Illuminate\\\Routing\\\Controller","object":{},"type":"->"}]', true);
    } else {
        return json_decode('[{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/xbot\/laravel-utils\/src\/Log\/Log.php","line":85,"function":"parseCallStack","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Notation.php","line":21,"function":"saveInput","class":"Ox3f\\\LaravelUtils\\\Log\\\Log","type":"::"},{"file":"\/Users\/xbot\/Sites\/sample-project\/app\/Api\/V1\/Controllers\/NotationController.php","line":32,"function":"incrNo","class":"App\\\Notation","type":"::"},{"function":"save","class":"App\\\Api\\\V1\\\Controllers\\\NotationController","object":{},"type":"->"},{"file":"\/Users\/xbot\/Sites\/sample-project\/vendor\/laravel\/framework\/src\/Illuminate\/Routing\/Controller.php","line":55,"function":"call_user_func_array"}]', true);
    }
}

class LogTest extends TestCase
{
    public function setUp()
    {
        Auth::shouldReceive('user')
            ->once()
            ->andReturn((object)['name' => 'jim',]);
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
        global $calledInController;

        // test being called in a plain method
        $calledInController = false;

        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | App\Notation::incrNo | Input:1');

        Log::saveInput(1);

        LaravelLog::shouldReceive('debug')
            ->once()
            ->with('jim | App\Notation::incrNo | Output:2');

        Log::saveOutput(2);

        // test being called in a controller action
        $calledInController = true;

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

最重要的是第一行，这里把测试类的命名空间设置得和测试对象一致，这样在下面重载的`debug_backtrace()`函数就会在测试对象中被优先使用。

在重载的函数中，通过全局变量`$calledInController`选择输出事先捕获的真实数据，从而mock出符合我们需要的函数。

