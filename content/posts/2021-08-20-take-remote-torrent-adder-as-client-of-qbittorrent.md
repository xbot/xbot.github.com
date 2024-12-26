---
title: 使用 Remote Torrent Adder 做 qBittorrent 的客户端
slug: Take Remote Torrent Adder as Client of Qbittorrent
date: 2021-08-20 00:03:28+08:00
tags:
- 浏览器
- 工具
- 下载
- 软件
- 计算机
---

我使用部署在 NAS 中的 qBittorrent 下载资源，每次手动复制磁力链链接到 qBittorrent 的 WebUI 很麻烦。

[Remote Torrent Adder](https://chrome.google.com/webstore/detail/remote-torrent-adder/oabphaconndgibllomdcjbfdghcmenci?hl=zh-CN) 是 Chrome 的扩展，可以通过右键菜单把 magnet 等协议的链接发送到多种下载工具，其中就包括 qBittorrent 。

安装过程不是那么顺利，这个开源小工具并没有对用户体验做更多的优化。

首先初次安装后，配置界面会有一个初始的、针对 Vuze SwingUI 的配置，需要把它删掉，再添加针对 qBittorrent v4.1+ WebUI 的配置，填好各项配置后选中 **Label/Directory
interactivity** 复选框。因为我把电影和剧集分目录存放，开始下载前需要指定存储目录。

第一次点菜单的时候没有反应，后来刷新页面后就好了。但是弹出来的指定下载目录的对话框没有选项，经测试发现第一次需要手工填写，之后有了缓存就可以选了。
