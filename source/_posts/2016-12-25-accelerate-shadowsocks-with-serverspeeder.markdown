---
layout: post
title: "用銳速加速Shadowsocks"
date: 2016-12-25 15:39
comments: true
categories: 計算機
tags:
- vps
- vultr
- bandwagon
- shadowsocks
---

各種VPS加速方案一般都是重復發包的原理，也就是用流量換速度。

先嘗試了kcptun，因為據說資源佔用很低，不過部署後打不開網頁。然後試了一下銳速，用一鍵腳本安裝，很方便，而且不像kcptun，不需要客戶端。部署前在Youtube只能看最低清晰度，現在在網絡條件好的情況下，720p也不卡，效果很明顯。

不過銳速不支持OpenVZ，所以搬瓦工用不了，我用的 [Vultr]() 東京機房的基礎配置。

## 參考
* [銳速破解版linux一鍵自動安裝包](https://www.91yun.org/archives/683)
