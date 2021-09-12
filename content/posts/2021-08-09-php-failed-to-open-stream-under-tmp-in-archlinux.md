---
title: "Archlinux 下 PHP 不能读取 /tmp 下文件的问题"
date: 2021-08-09T15:27:58+08:00
slug: PHP Failed To Open Stream Under Tmp In Archlinux
categories:
- 计算机
tags:
- Archlinux
- PHP
---

问题的场景是，以 http 用户运行的 FPM 进程访问 `/tmp` 下由普通用户运行的 CLI 进程创建的文件时，报如下错误：

> Failed to open stream.

原因是 PHP-FPM 的 systemd 配置中默认对 FPM 进程单独挂载 `/tmp` 目录。

具体对应：

`/usr/lib/systemd/system/php-fpm.service`

中的：

`PrivateTmp=true`
