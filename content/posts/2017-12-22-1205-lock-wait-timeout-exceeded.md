---
title: Laravel单元测试错误：1205 Lock wait timeout exceeded
date: 2017-12-22 15:47:32
categories:
- 计算机
tags:
- 编程
- 单元测试
- php
- Laravel/Lumen
---
错误信息：

> 1205 Lock wait timeout exceeded; try restarting transaction

原因之一是测试用例里重写的tearDown()方法中没有调用父类的该方法。因为DatabaseTransaction这个trait自动开启了事务，对应的回滚方法在测试用例基类的tearDown()中执行。


