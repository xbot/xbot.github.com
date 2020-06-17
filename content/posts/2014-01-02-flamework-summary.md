---
layout: post
title: "PHP框架实战（∝）：烈焰之终章"
date: 2014-01-02 15:15:00
comments: true
categories:
- 计算机
tags:
- PHP
- Flamework
- 框架
- 编程
---

写“烈焰”（Flame）用了一周的业余时间，主要是对平时一些想法的总结和验证。实现了比较完整的控制器层和视图层，对模型层的ActiveRecord实现思路做了一下梳理。

当然，一个可实用的框架需要包含的东西远不止这些。比如框架中用到代码动态调用的地方，一定要做好语言安全子集的过滤，否则就是很大的安全漏洞。再比如需要支持依赖反转的缓存机制，实现对多种缓存方式的平滑支持。此外，像URI路由、可扩展、多模板方案支持也都是现代框架的标配。这些留待以后有时间再讨论。然而在这次练习的过程中，我突然想到一个问题——PHP是不是适合实现一个完备的框架。

曾见过一句话，说PHP本身就是一个框架，后来明白，这才是微言大义。PHP有很多高级选项、高级函数和扩展，用得好事半功倍，用不好就是魔鬼。

PHP本身有很多问题，协议不统一、函数命名混乱、命名空间语法怪异而且鸡肋等等都是老生常谈。在运行模式上，无论是Apache+PHP模块，还是NGINX+FastCGI，都只能实现在纵向层面上对一次请求的处理，由于缺乏在内存中持续运行程序的机制，凡是对程序全局共享并持续占有的东西都不能实现，比如数据库连接池等，以至于很多初始化的工作对于每次请求都要重新执行一次，这意味著面向对象越彻底、封装越多，系统资源的重复消耗越厉害，所以PHP的程序在性能和内存占用上与Java相比有一定缺陷。因此PHP更适合短平快的应用场景，不适合实现复杂的业务逻辑。

基于这个观点，我认同混合编程。没有哪种语言是完美的，用对的工具做对的事是最理想的。用PHP实现一个完备的框架也许不是个明智的选择，从短平快的角度出发，它更适合用来实现微框架。

现在微框架是个比较热门的话题，我最早接触的是Python的Bottle和Flask，短小精悍，非常容易上手。微框架主要实现控制器层和视图层，一般不包括模型层。为了以最快的速度将请求路由到处理逻辑，一般以最直接的方式建立URI模板和回调物件之间的映射，控制器层可以以极简的方式实现，例如只做一个像本文后面例子中那样简单的约定。微框架应该尽可能少地包含配置，大部分时候并不需要像Java的S.S.H那样滥用配置，[CoC原则](http://en.wikipedia.org/wiki/Convention_over_configuration)就持这样的观点，约定可以解决的问题就不要用配置去做。

下面只使用两个函数和五条约定实现一个微框架：

```php
<?php
/**
 * 路由定义与应用
 * @param string $route 用作定义路由规则时，此参数为模板字符串，
 *     使用冒号加参数名作为参数占位符，例如：
 *         on('/post/edit/:id', function($id){});
 *     用做应用路由规则时，此参数为URI，例如：
 *         on($_SERVER['REQUEST_URI']);
 * @param callable $callback 路由规则的回调逻辑，如果路由规则中
 *     含有参数占位符，回调中需存在同名的参数；当函数作为应用路
 *     由规则使用时，此参数不指定
 * @return void
 * @since 1.0
 */
function on($route, $callback) 
{
    static $routes = array();
    $regex = '#'.preg_replace('#:[^\/]+#', '.*', $route).'#';
    $routes[$route] = array($regex, $callback);
    if (is_null($callback)) {
        foreach ($routes as $r=>$cfg){
            if (preg_match($cfg[0], $route)) {
                $params = array();
                $idx = strpos($r, ':');
                if (is_int($idx)) {
                    $keys = explode('/', substr($r, $idx));
                    $keys = array_map(function($v){ return trim($v, ':'); }, $keys);
                    $values = explode('/', substr($route, $idx));
                    $params = array_combine($keys, $values);
                }
                call_user_func_array($callback, $params);
                break;
            }
        }
        echo '404';
    } 
}

/**
 * 视图渲染函数
 * @param string $view 视图名称
 * @param array $params 关联数组，包含需要填到视图模板中的参数键值对
 * @return void
 * @since 1.0
 */
function render($view, $params=array()) 
{
    extract($data, EXTR_PREFIX_SAME, 'tpl_');
    $viewFile = dirname(realpath(__FILE__)).DIRECTORY_SEPARATOR.'view'
        .DIRECTORY_SEPARATOR.$view.'.php';
    if (is_readable($viewFile)) {
        require($viewFile);
    } else {
        throw new Exception("View template $view does not exist or cannot be readable.");
    }
}
?>
```

on()身兼两用，一是定义路由规则和对应的响应逻辑，一是对指定URI应用路由规则。render()的作用是渲染视图模板。用法如下：

```php
<?php
include 'micro.php';

on('/post/save', function(){
    echo "Post saved.\n";
});

on('/mail/send/:address/:title', function($address, $title){
    echo "write letter to $address with title $title\n";
});

on($_SERVER['REQUEST_URI']);
?>
```

约定如下：

  1. 每个Controller作为一个文件放在项目根目录下的controller目录中，名称即文件名，后缀是“.php”；Action对应于Controller中的各个函数（注意过滤语言安全子集）；
  - 使用php.ini的配置项“auto_prepend_file”和“auto_append_file”实现过滤器；
  - 使用“set_error_handler()”和“set_exception_handler()”自动处理异常和错误；
  - 使用PDO实现数据库抽象层；
  - 视图模板用PHP脚本实现，模板文件放在当前目录下的view目录里，模板文件名即模板名，后缀为“.php”；

当然这离实际可用还差得远，这里只是说明一下微框架的基本理念：第一，打狗不需要金箍棒；第二，大部分项目都是在打狗。结合混合编程，这一点会更明显。
