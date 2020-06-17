---
title: 对比Laravel Excel和Spout的读写性能
slug: performance comparation for laravel excel and spout
date: 2019-07-07 20:23:26
categories:
- 计算机
tags:
- 编程
- php
- Laravel/Lumen
---

```
./artisan excel:write --driver=spout --amount=100000
写入数据100000行。
用时：156秒。
最大使用内存：2750.5106964111 M。

./artisan excel:write --driver=laravel-excel --amount=100000
写入数据100000行。
用时：305秒。
最大使用内存：1330.7370758057 M。

./artisan excel:read ./storage/app/test.xlsx --driver=spout
读取数据100001行。
用时：576秒。
最大使用内存：122.15303039551 M。

./artisan excel:read ./storage/app/test.xlsx --driver=laravel-excel
读取数据100001行。
用时：166秒。
最大使用内存：739.48976898193 M。
```

基本上，Laravel Excel写XLSX的性能是Spout的一半，但内存占用是对方的一半。而在读文件上，前者的性能是后者的约3倍多，而内存占用是后者的6倍。

测试环境：

- MBP 3.1 GHz Intel Core i5 8G内存
- PHP 7.1.30
- Laravel 5.8.27
- Laravel Excel 3.1.14
- Spout 3.0.1
