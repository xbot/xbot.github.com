---
title: 自动下载豆瓣想看列表中的影视剧
slug: Auto download tv series and movies from douban want to watch list
date: 2025-08-04T20:12:36+08:00
tags:
  - 计算机
  - 家庭自动化
  - homelab
  - NAS
  - n8n
  - Jellyseerr
toc: false
draft: false
---

前些天写了个自动下载豆瓣想看列表中电视剧的 n8n 工作流，这两天完善了一下，电影也支持了。只要在豆瓣上标记想看的影视剧，就会自动通过 Jellyseerr 分发给 Radarr 或 Sonarr ，一旦有资源就会自动下载并通知到手机，打开电视就可以看了。

![](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2025-08-04-20-14-05-mac_20250803114650.jpeg)