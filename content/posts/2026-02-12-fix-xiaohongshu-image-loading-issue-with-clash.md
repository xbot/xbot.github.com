---
title: "解决 Clash 导致小红书网页版图片无法显示的问题"
slug: "fix-xiaohongshu-image-loading-issue-with-clash"
date: 2026-02-12T20:30:00+08:00
draft: false
tags:
  - 计算机
  - Clash
  - OpenClash
  - Stash
  - homelab
  - 网络
---
家里通过运行了 OpenClash 的旁路网关上网，手机和电脑上则用 Stash ，两者都是 Clash 的客户端。最近发现一个恼人的问题：每隔一段时间，小红书网页版的图片就集体罢工，只剩一片空白。

![小红书图片加载失败](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20260212200814939-ee7996ac33de8c8202341d2a6e265153.avif)

打开浏览器开发者工具一看，图片请求全报了同一个错误：

```
net::ERR_SSL_VERSION_OR_CIPHER_MISMATCH
```

这个错误的意思是 TLS 握手失败了。原因在于 Clash 的 Fake IP 模式。开启 Fake IP 后，Clash 会对 DNS 请求返回一个虚假的 IP 地址（通常是 198.18.x.x 段），浏览器拿着这个假 IP 发起 HTTPS 连接，由 Clash 拦截后再转发到真实服务器。问题出在小红书 CDN（xhscdn.com）的 TLS 配置比较严格，Clash 中转连接时 TLS 握手参数无法正确协商，于是就报了这个 SSL 错误，图片自然加载不出来。

解决办法很简单：让 Clash 对小红书 CDN 的域名不使用 Fake IP ，直接返回真实 IP 。

**OpenClash** 在配置文件 `/etc/openclash/custom/openclash_custom_fake_filter.list` 中添加一行：

```
*.xhscdn.com
```

**Stash** 的配置路径是：Settings → Config File → Visualization Editor → DNS → Fake IP Filter ，把 `*.xhscdn.com` 加进去即可。

改完之后小红书图片立刻恢复正常。
