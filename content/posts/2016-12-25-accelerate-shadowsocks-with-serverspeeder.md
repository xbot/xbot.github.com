---
layout: post
title: "用锐速加速Shadowsocks"
slug: accelerate shadowsocks with serverspeeder
date: 2016-12-25 15:39:00
comments: true
categories:
- 计算机
tags:
- vps
- vultr
- bandwagon
- shadowsocks
---

各种VPS加速方案一般都是重复发包的原理，也就是用流量换速度。

先尝试了kcptun，因为据说资源占用很低，不过部署后打不开网页。然后试了一下锐速，用一键脚本安装，很方便，而且不像kcptun，不需要客户端。部署前在Youtube只能看最低清晰度，现在在网络条件好的情况下，720p也不卡，效果很明显。

不过锐速不支持OpenVZ，所以搬瓦工用不了，我用的 [Vultr]() 东京机房的基础配置。

## 参考
* [锐速破解版linux一键自动安装包](https://www.91yun.org/archives/683)
