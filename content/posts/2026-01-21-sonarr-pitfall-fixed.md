---
title: "用 Sonarr 追剧的坑，我填上了"
slug: "sonarr-pitfall-fixed"
date: 2026-01-21T00:10:29+08:00
draft: false
tags:
- 青梅煮酒
- Sonarr
- qBittorrent
- homelab
---
最近追的一些美剧经常会提前一周下载到下一集的资源，当然都不是真实内容，大部分是恶意资源。如果在剧集播出之后才下载，就能减少这个问题的发生，然而 Sonarr 竟然没提供这个功能。

于是写了一个脚本，在 qBittorrent 下载完后自动执行，如果下载的资源小于 500MB 就认为是垃圾资源。由于配置了 qBittorrent 自动过滤掉 rar、iso 等常见的恶意文件后缀，所以这个规则几乎不会出错。判定后调用 Sonarr 的接口，删除下载任务并拉黑种子。
