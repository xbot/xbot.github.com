---
title: 让 AdGuard Home 和 OpenClash 协同工作
slug: Make Adguard Home and Openclash Work Together
date: 2022-03-06 12:12:10+08:00
tags:
- OpenWrt
- WireGuard
- OpenClash
- 翻墙
- HomeLab
- AdGuard
- 计算机
---

# 家庭网络环境

- 使用 OpenWrt （*以下简称 OP* ）做旁路网关
- OP 内建 OpenClash （*以下简称 OC* ）访问不存在的网站
- OP 内建 WireGuard （*以下简称 WG* ）做内网穿透
- 使用 AdGuard Home （*以下简称 AH* ）做内网 DNS 和广告过滤

# 之前遇到的问题

不管怎样配置，内网和 WG 下的内网主机名解析和科学上网这 4 种场景总有至少一个不工作。

期间创建过一个虚拟机做独立的 WG Server ，内建 SmartDNS （*以下简称 SD* ）做 AH 的上游，其它场景工作得很好，只是无法通过旁路网关访问不存在的网站。

# 解决方法

首先如果要正常使用 OC ，就必须使用它的 DNS 解析。

同时，又要达到使用 AH 做内网主机名解析和广告过滤的目的，所以要把 OC 设成 AH 的唯一上游。

然后最棘手的问题来了。如果把 AH 设成 dnsmasq 的上游， WG 下不能解析内网主机名。如果把 AH 设成监听 53 端口，则内网无法解析主机名。

想到之前在独立的 WG Server 上用 SD 在两种场景下都可以正常解析，就试着用它代替 dnsmasq ，结果柳暗花明，所以场景都跑通了。

# 遗留的问题

1. OP 内置的 AH 在监听 53 端口时，为什么内网无法通过它解析？（*与 SD 一样，监听的是“:::53”*）
1. OP 内置的 AH 作为 dnsmasq 的上游时，为什么内网可以解析而 WG 不能？
