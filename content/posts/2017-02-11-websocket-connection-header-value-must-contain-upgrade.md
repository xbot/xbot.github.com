---
title: "WebSocket: Connection Header Value Must Contain Upgrade"
slug: websocket connection header value must contain upgrade
date: 2017-02-11 21:52:30
categories:
- 计算机
tags:
- websocket
- 编程
---

用NGINX反向代理实现WSS后，创建连接时报错：

> Error during WebSocket handshake: 'Connection' header value must contain 'Upgrade'

根据[官方文档](http://nginx.org/en/docs/http/websocket.html)，NGINX从1.3.13开始才支持这个特性，所以解决方法是升级到最新版。