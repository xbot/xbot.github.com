---
title: Mock Laravel的DB Facade的方法
slug: how to mock the db facade of laravel
date: 2017-08-14 15:51:41
categories:
- 计算机
tags:
- 编程
- 单元测试
- php
- Laravel/Lumen
---
关于数据库操作是否应该mock的问题，我认为查询一般不应该mock，可以避免一些问题，但如果查询的结果可能影响到断言，就应该mock了。

Laravel的DB facade完整的mock实例是这样的：

{{< gist xbot d0ace9c1ebdb161adf229c9a9c62f2bf >}}
