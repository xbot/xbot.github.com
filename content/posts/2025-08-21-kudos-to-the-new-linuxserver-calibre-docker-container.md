---
title: "LinuxServer 的新版 Calibre 镜像很好，但是……"
slug: "kudos-to-the-new-linuxserver-calibre-docker-container"
date: 2025-08-21T19:04:33+08:00
draft: false
tags:
  - 青梅煮酒
  - Calibre
  - Docker
---
最近，LinuxServer 发布了新版的 Calibre Docker 镜像，用 Selkies 替代原来的 Kasm VNC 作为浏览器访问界面。

新版本好用多了，支持直接输入中文了，进一步降低了对桌面版 Calibre 的依赖。

不过有个问题，在 macOS 上默认会打开 HiDPI ，导致在高分辨率屏幕上有可能无法显示界面。此时拖动浏览器窗口，缩小窗口大小，直到界面显示正常，然后把 Selkies 的 HiDPI 选项关了就行了。
