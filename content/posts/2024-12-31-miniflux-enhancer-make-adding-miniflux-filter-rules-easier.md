---
title: 写了个方便添加 Miniflux 过滤规则的 Chrome 扩展
slug: miniflux enhancer make adding miniflux filter rules easier
date: 2024-12-31T23:02:02+08:00
tags:
  - 计算机
  - 浏览器
  - Miniflux
  - Chrome-扩展
toc: false
draft: false
---

Miniflux 是个可以自己部署的 RSS 阅读器，我用它省了每年几百块的 Inoreader 订阅费。

为了提高阅读效率，经常需要给订阅源添加一些过滤规则，过滤掉不关心的内容。

但是每次都修改订阅源的配置太麻烦了，所以我写了个 Chrome 浏览器的扩展，在每条阅读条目下面添加一个“更新规则”的按钮，这下方便多了。

![2024-12-31-23-04-53-introduction-01-update-rules](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-31-23-04-53-introduction-01-update-rules.png)

https://github.com/xbot/chrome-extension-miniflux-enhancer
