---
title: 利用单元测试为开发提效
slug: use unit test as an efficiency tool
date: 2019-08-13 16:38:02
categories:
- 计算机
tags:
- 编程
- 单元测试
- php
- Laravel/Lumen
---

单元测试可以方便地伪造和回滚测试数据，这个特性决定了它其实是一个效率工具。

例如在开发退款单、发票的时候，由于这种后置的功能依赖订单、支付、库存等前置的数据，因此需要频繁地创建这些测试数据，这就导致开发的效率极低，有时甚至需要切换多个系统做诸如增加库存、下单、支付等操作。虽然在开发环境可以通过直接修改数据使之恢复可被测试的状态，但仍然存在效率很低、遗留垃圾数据和存在隐患等问题。

单元测试可以完美地解决这些问题。

<!--more-->

## 伪造测试数据

在Laravel/Lumen中，利用Model Factory、Faker可以很方便地伪造测试数据。进一步地，只需要伪造被测单元依赖的数据，不需要真实、完整的数据。这些在[《单元测试的技巧》](/phpunit-tips)一文中有详细介绍。

测试数据只是被测单元依赖关系的一种，利用Mockery框架对依赖关系做伪造和断言是非常重要的知识。Mock是单元测试的灵魂！

## 回滚数据库

Laravel/Lumen提供了DatabaseTransactions和DatabaseMigrations（不同版本可能有差异）这样的Trait，可以自动在每个测试方法执行后自动回滚数据库。

## 调试代码

不管是调试测试代码还是被测单元，调试代码在结合单元测试的开发过程中都是个强需求。通常调试代码有两种方式：打印并中断（echo & die）和单步调试。这两者在单元测试中都可以很方便地实现。

### 打印并中断

某些版本的PHPUnit会抑制标准输出，某些配置项（`beStrictAboutOutputDuringTests="true"`）也会导致在有标准输出时中断单元测试的执行。可通过把信息打印到标准错误输出设备绕过：

```php
fwrite(STDERR, "Behold:\n" . var_export($order, true) . "\n");
```

### 单步调试

单元测试是通过命令行执行的，因此可以利用XDebug支持CLI的特性触发单步调试：

```shell
export XDEBUG_CONFIG="idekey=VSCODE"
```

## 性能调优

和单步调试一样，可以通过环境变量实现：

```shell
export XDEBUG_CONFIG="idekey=VSCODE profiler_enable=1"
```

但是性能分析会极大地降低代码执行效率，因此建议通过命令参数的方式实现：

```shell
php -d "xdebug.profiler_enable=1" ./vendor/bin/phpunit
```

