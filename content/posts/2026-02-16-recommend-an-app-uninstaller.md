---
title: "推荐一个卸载 App 的工具"
slug: "recommend-an-app-uninstaller"
date: 2026-02-16T10:00:00+08:00
draft: false
tags:
  - 青梅煮酒
  - app
---
平时卸载 App 都是用 RayCast 内置的 Uninstall Application 功能，今天想卸载讯飞输入法的时候，发现 RayCast 里怎么也找不到它。

原因是讯飞输入法不在 `/Applications/` 目录下，而是安装在 `/Library/Input Methods/`，RayCast 只扫描了前者，自然找不到。

想起来 Mole 也有卸载 App 的功能，试了一下，顺利找到了讯飞输入法，而且清理得很彻底，残留文件和缓存都一并删除了。

[Mole](https://github.com/tw93/Mole) 是一个 macOS 下的命令行工具，主要用途是清理磁盘空间，同时也提供了卸载 App 的功能。相比 GUI 工具，它的优势就在于不局限于 `/Applications/` 目录，能覆盖到更多安装位置。

![](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20260216190916618-32583506eebe4ee2e16275bc4f351ed3.avif)
