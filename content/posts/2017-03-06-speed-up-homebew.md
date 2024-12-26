---
title: 加速Homebrew
slug: speed up homebew
date: 2017-03-06 23:14:02
tags:
- mac
- homebrew
- 计算机
---

两个方法：走代理和使用国内镜像。镜像有同步时间差，而且遇到国外资源还是慢。

homebrew用curl下载，用proxychains和环境变量http_proxy都没用，需要在`~/.curlrc`里配置：

```
socks5 = "127.0.0.1:1080"
```
