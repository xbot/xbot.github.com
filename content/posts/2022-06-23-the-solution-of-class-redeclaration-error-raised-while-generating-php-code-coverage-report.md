---
title: "解决 PHPUnit 生成代码覆盖报告时抛出重复定义类的错误的办法"
slug: "The Solution of Class Redeclaration Error Raised While Generating Php Code Coverage Report"
date: 2022-06-23T14:42:34+08:00
categories:
- 计算机
tags:
- PHP
- phpunit
- 单元测试
- 编程
---

在生成 Cobertura 格式的代码覆盖报告时程序报了如下错误：

> Generating code coverage report in Cobertura XML format ... PHP Fatal error:  Cannot declare class App\FakeNamespace\GenericProvider, because the name is already in use in /builds/fake_project/app/FakeNamespace/GenericProvider.php on line 17

对应的代码和单元测试执行均无问题。

经排查，直接原因是使用了 Mockery 的 [Instance Mocking](http://docs.mockery.io/en/latest/reference/instance_mocking.html) [^mockery_overloading]特性 mock 代码中的硬依赖关系（[Hard Dependency](https://robertbasic.com/blog/mocking-hard-dependencies-with-mockery/)）。

该特性的实现利用了类的自动加载机制和依赖注入技术。对依赖关系 mock 的实例保存在 Mockery 的容器中，同时生成一个 stub 文件：

```php
<?php

namespace Mockery;

class Mockery_App_FakeNamespace_GenericProvider
{
    private Container $container;

    function doSomething($param)
    {
        // internal code check expectations

        return $this->container->get('App\FakeNamespace\GenericProvider::doSomething')->expectations();
    } 
}
```

当代码中实例化这个类的时候，Mockery 自动加载该文件，从而代替了对原类文件的加载。

此后，如果再次 `require` 原类文件，就会报文章开头提到的错误。

解决办法是重构代码，把硬依赖关系改为使用依赖注入实现。例如在 Laravel 中通过容器获取对象实例，而不是直接 `new` 一个类。

[^mockery_overloading]: https://docs.mockery.io/en/latest/reference/creating_test_doubles.html#overloading