---
title: Pi-hole v6 的几个问题
slug: several issues with pihole v6
date: 2025-02-20T12:51:04+08:00
tags:
  - 青梅煮酒
  - homelab
  - Dnsmasq
  - Pi-hole
  - PBS
  - 数据备份
  - DNS
toc: false
draft: false
---

昨天 Pi-hole 自动升级到了 v6 ，出了几个大问题。

## dnsmasq 的自定义规则失效

v6 默认不起用 dnsmasq 的自定义规则，需要在设置里手动打开。

## 隔一会儿出现一次 DNS 解析超时的问题

每隔几十秒或几分钟就会出现 DNS 超时的问题，导致访问网络要么失败、要么很慢。暂时没有找到解决办法。

## Web UI 执行变慢

访问任何一个页面或者保存设置都比 v5 慢了很多。

官方论坛也是哀鸿遍野。好在从 PBS 恢复备份非常简单，擦干眼泪，把 Docker 容器的标签限制到 v5 了。