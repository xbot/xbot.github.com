---
title: 调校mycli
date: 2017-06-26 18:05:31
categories:
- 计算机
tags:
- 工具
- 数据库
---

做以下配置，使mycli按需使用pager，并在数据过多时不破坏表格格式：

```ini
# ~/.my.cnf

[client]
pager = less -FSXR

```
