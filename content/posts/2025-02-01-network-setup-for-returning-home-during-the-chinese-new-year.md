---
title: 网络设置的“春节限定版”
slug: network setup for returning home during the chinese new year
date: 2025-02-01T16:03:23+08:00
tags:
  - 青梅煮酒
  - 春节
  - 网络
  - AppleTV
  - Stash
  - WireGuard
  - Tailscale
  - Dnsmasq
  - mosdns
  - homelab
toc: false
draft: false
description: 春节回家的网络设置
---
春节回家，为了获得更好的网络体验，我带上了 Apple TV ，装上 Stash 当作旁路网关，访问外网的体验确实不错。

与此同时，我还得使用住处服务器上部署的各类服务。除了一直使用的 WireGuard ，我另外部署了 Tailscale 作为备份线路，以此确保网络连接稳定。然而，问题也接踵而至，DNS 的设置变得更为复杂。由于旁路网关采用 Fake IP 方式，访问外网时 DNS 必须使用旁路网关自身的；而访问住处内网主机则需用部署在内网的 DNS ，这就需要进行 DNS 分流。

最简便的方法，是在每台设备上维护一份内网主机名与IP地址的映射关系，但维护成本过高，不太可行。

也能用 Dnsmasq 。它安装容易、配置简单，不过缺点也很明显，回到住处还得手动修改默认的上游 DNS ，还是有些麻烦。

最终，我选择了 mosdns 。它也存在缺点，比如 Homebrew 没有现成的安装包，需要手动部署；官方文档简洁，得结合 Github 仓库讨论区他人的配置文件来理解。但它的优点同样突出，可在不同地点自动选择默认的上游 DNS ，且支持正则表达式，匹配主机名、域名比 Dnsmasq 更精准。

![2025-02-01-20-04-13-DNS分流拓扑图](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2025-02-01-20-04-13-DNS%20分流拓扑图.svg)