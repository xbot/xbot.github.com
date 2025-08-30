---
title: 给 Miniflux 实现瀑布流式布局
slug: Implement masonry-style layout for Miniflux
date: 2025-06-30T23:04:41+08:00
tags:
  - 青梅煮酒
  - Miniflux
  - RSS
  - 阅读
  - 浏览器
  - Chrome-扩展
toc: false
draft: false
---
用来用去还是 Inoreader 最符合我的阅读习惯，但是价格接二连三地涨到很高的程度，所以我才换到了 Miniflux 。后者也能跑通我的阅读流程，只是 UI 过于简洁，只有条目列表，对于模型手办、购物信息这种订阅源还是预览图瀑布流的形式更高效。

给 Miniflux Enhancer 加了点功能，在 Miniflux 的列表上显示预览图，配合自定义 CSS 样式可以对特定的分类或源实现大预览图瀑布流式布局。

![](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20250630230713000-f7dd2f17c0d9dc0062ab1861f631d7ec.avif)