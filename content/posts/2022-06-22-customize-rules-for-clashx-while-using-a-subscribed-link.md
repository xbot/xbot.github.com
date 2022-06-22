---
title: "ClashX 在使用订阅链接的同时添加自定义规则的方法"
slug: "Customize Rules for Clashx While Using a Subscribed Link"
date: 2022-06-22T22:15:12+08:00
categories:
- 计算机
tags:
- clash
- 翻墙
---
在 ClashX 中使用机场的订阅链接时，如果需要添加自定义的规则，且在定时更新订阅的链接时不会被覆盖，解决的方法是创建一个新的配置文件，使用 Clash 的 [proxy-providers](https://github.com/Dreamacro/clash/wiki/configuration#proxy-providers) 和 [rule-providers](https://github.com/Dreamacro/clash/wiki/premium-core-features#rule-providers) 分别引用订阅的链接和开源的规则集：

{{< gist xbot 26f540ae8fad51cf27152f2385caccee >}}

然后就可以在 `rules` 区块里添加自定义规则了。
