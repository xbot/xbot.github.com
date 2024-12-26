---
title: 用微力同步换掉了 Dropbox
slug: Replace Dropbox with VerySync
date: 2021-10-06 01:24:41+08:00
tags:
- Dropbox
- 微力同步
- NAS
- 最佳实践
- 计算机
---

我希望找到一种体验较好的方式，把两部手机和索尼黑卡上的照片集中同步到 NAS 上交由 PhotoPrism 托管。

需求如下：

1. 可以方便地发送多平台照片到 NAS 。
2. 可以单向同步手机到 NAS 并忽略手机的删除操作，以节省手机的存储空间。
3. 可以实现内网穿透且简单稳定。
4. 便宜。

对比了 PhotoSync 、 Resilio Sync 、 Syncthing 和微力同步（verysync）。 

PhotoSync 作为专门针对同步照片的场景设计的 APP ，完美覆盖前两点，第三点可以借助 NAS 已经配置好的内网穿透实现，但是需要购买相应的 Plan ，而且 Android 和 iOS 平台要各自单独购买。

Resilio Sync 实现内网穿透需要引入新的依赖关系，不完全满足第三点。

Syncthing 看起来不错，但是没有官方的 iOS 客户端。

微力同步据说是基于 Syncthing 的，且补上了后者缺失的一环。各平台免费，只有 iOS 上需要花 12￥ 买 APP 。

我的 NAS 运行的是 Unraid ， Community Applications 里没有微力同步，在[这里](https://github.com/shuosiw/unraid)找到了别人配置好的模板，部署很方便。

试用后因为太好用，干脆把 Dropbox 目录也放到微力同步里，卸载了会占用很多内存且免费方案 3 个端的限制早已捉襟见肘的 Dropbox 。
