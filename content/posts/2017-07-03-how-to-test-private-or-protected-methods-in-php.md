---
title: 怎样测试PHP的Private或Protected方法
slug: how to test private or protected methods in php
date: 2017-07-03 13:17:30
tags:
- 编程
- 测试
- PHP
- 计算机
---

利用闭包绑定：

```php
$ctrlr = new UserController;

$tester = function () use ($uid) {
    $this->getUser($uid);
};
$runner = $tester->bindTo($ctrlr, $ctrlr);
$runner();
```
