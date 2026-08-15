---
title: "OpenClash 导致 KOReader 误判网络不可用"
slug: "openclash-koreader-network-false-offline"
date: 2026-08-15T21:18:33+08:00
draft: false
tags:
  - 青梅煮酒
  - Kindle
  - KOReader
  - OpenClash
toc: false
---

将 Kindle 的网关和 DNS 都配置为运行 OpenClash 的旁路网关后，原生系统可以正常联网，KOReader 中却提示网络不可用。

原因是 KOReader 会通过解析 `dns.msftncsi.com` 判断网络是否可用。OpenClash 默认的 Fallback Filter 指定这个域名使用 Fallback DNS 解析，而默认的 Fallback DNS 是无法直接访问的 Cloudflare 和 Google DoH。解析请求因此超时，KOReader 也就误判为没有网络。

解决方法是在 OpenClash 的“自定义上游 DNS 服务器”中，配置 DNS 的 `Fallback` 组的 Cloudflare 和 Google DoH 的“指定策略组”让它们走代理。保存并重启 OpenClash 后，`dns.msftncsi.com` 可以正常解析，KOReader 的网络检测也随之恢复正常了。
