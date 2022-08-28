---
title: "我的第一台 NAS"
slug: My First NAS
date: 2021-10-10T21:50:12+08:00
categories:
- 青梅煮酒
tags:
- NAS
- 数码
---

![2021-11-09-00-57-46-my-first-nas](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2021-11-09-00-57-46-my-first-nas.jpg)

年初终于攒了一台 NAS 。

之前用一块 1T 的 Buffalo 外置硬盘，但是多年之后，我开始担心这头老水牛还能活多久，况且它的剩余空间也已经不多了。与此同时，手机和电脑的存储空间也像油腻大叔的头发一样越来越少，我必须把沉淀的冷数据迁移出去。身边像有一群饥饿的东西拼命向我伸手。

而且，随着数码设备的增加，尤其在添了一台 Apple TV 后，出于看 YouTube 的目的，我更需要通过软路由的方式改善家庭网络的翻墙体验。

再者，以前看影剧都是把 U 盘插到电视或者微投上，或者通过电脑串流，多少也有点麻烦。

于是打算用一台 All-in-one 解决所有问题。

## 硬件规格

最初考虑过群晖、威联通之类的品牌 NAS ，如果能得到更好的使用体验，多花点钱也是可以的。但是看过硬件规格后，感觉我要是以这个价格买了，那就算人生的污点了。

所以开始准备攒机，按优先级列举一下要求：

1. 美观，不要傻大黑粗的直男风。
2. 体积小。
3. 兼顾功耗和性能。
4. 内存大（16 ~ 32 G）。

先后拟了 4 套配置，和某淘宝店的套装做了一下对比：

|   -    | 淘宝店方案          | 价格 | 方案 1             | 价格 | 方案 2             | 价格 | 方案 3              | 价格 | 方案 4                       | 价格 |
| :----: | ------------------- | ---- | ------------------ | ---- | ------------------ | ---- | ------------------- | ---- | ---------------------------- | ---- |
|  机箱  | 万由 410            | -    | 万由 410           | 680  | 迎广 MS04          | 930  | 蜗牛星际B涂装改风扇 | 275  | 蜗牛星际B涂装改风扇          | 275  |
|  电源  | 益衡 200W           | -    | 益衡 7025B         | 236  | 机箱内置           | -    | 益衡 7025B          | 236  | 益衡 7025B                   | 236  |
|  主板  | B365-ITX 定制工控板 | -    | 华擎 Z390M-ITX/ac  | 1199 | 华擎 Z390M-ITX/ac  | 1199 | 华擎 Z390M-ITX/ac   | 1199 | 华擎 Z390M-ITX/ac（二）      | 700  |
|  CPU   | I5-8600T            | -    | I5-8600T           | 825  | I5-8600T           | 825  | I5-8600T            | 825  | I5-8600T                     | 825  |
| 散热器 | -                   | -    | AVC 28mm 115X      | 23   | AVC 28mm 115X      | 23   | AVC 28mm 115X       | 23   | AVC 28mm 115X                | 23   |
|  内存  | 镁光 DDR4 16G 2666  | -    | 光威 DDR4 2666 16G | 379  | 光威 DDR4 2666 16G | 379  | 光威 DDR4 2666 16G  | 379  | 海盗船 DDR4 3000 16G×2（二） | 700  |
|  总计  | -                   | 2999 | -                  | 3342 | -                  | 3356 | -                   | 2937 | -                            | 2759 |

最后选了第 4 种。

其实最难挑的是机箱，专门给 NAS 用的本来就少，大多数还都是没有设计感的商务风，所以最终选了个人喷涂改装的蜗牛星际 B 款机箱。喷涂质量一般，细节粗糙，远看还行。内部空间很逼仄，费了半天功夫也没装上主板，只好用钳子把主板支架的一角掰弯才搞定。

主板只能用 ITX 小板，比 ATX 板贵很多，买的二手，降低成本。

CPU 用当时比较热门的 i5-8600T ，散片刚从 1k+ 降到 800+ 。6 核， T 系列，兼顾性能和功耗。

主板卖家同时也在卖两条海盗船的 16 G 内存，就打包一起买了。

硬盘用了 4 块 4T 的酷狼，后来又加了一条 500G 的 M.2 SSD 当缓存盘，实现硬盘自动休眠。

## 软件平台

选系统的时候纠结了一阵子，一旦选定了可能会用相当长的时间，不想因为换系统倒腾数据。

列了一下需求，按优先级依次是：

1. 稳定、安全。
2. 使用方便，功能完善，体验好。
3. 支持 Docker 和虚拟机，性能损失小。
4. 界面美观。
5. 可以方便地实现硬件直通和硬盘自动休眠。

对比了几种常见的 NAS 系统。虽然群晖的系统如雷贯耳，但是对黑群的稳定性有顾虑。ESXi 是商业软件，据说用户体验不错，不过是纯粹的虚拟机平台，不是基于 Linux 的，所以不支持原生的 Docker 。 PVE 基于 Debian ，但是可视化界面比较简陋，需要大量命令行操作和配置。 TrueNAS 以健壮性和 ZFS 著称，但是基于 BSD ，不原生支持 Docker 。

剩下一对瑜亮，OVM 和 Unraid 。 OVM 基于 Debian ，界面简洁易用 ，功能较完善，开源免费，如果没有后者，我肯定就用它了。 Unraid 是商业软件，功能的完备程度、界面的美观程度和易用性都要好于 OVM ，通过校验盘实现冗余备份的方式可以较简单地增减硬盘。

最后买了 Unraid 的 Basic Plan 。

![2021-11-09-01-16-20-1iSmYb](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2021-11-09-01-16-20-1iSmYb.png)

## 使用感受

目前主要使用以下功能：

| 服务                         | 功能                                     |
| ---------------------------- | ---------------------------------------- |
| AdGuard Home                 | DNS 缓存、广告过滤                       |
| Archlinux VM + Redis + MySQL | 开发环境                                 |
| Cloudflare-DDNS              | DDNS                                     |
| DDNSTO                       | 备用内网穿透                             |
| DokuWiki                     | 个人维基                                 |
| Heimdall                     | 导航页                                   |
| Jellyfin                     | 媒体中心                                 |
| NAS                          | 存储个人数据、监控视频、 Calibre Library |
| OpenWrt                      | 旁路网关                                 |
| PhotoPrism                   | 照片管理                                 |
| SWAG                         | Let's Encrypt 证书申请/维护、反向代理    |
| Time Machine                 | 备份 Macbook Pro                         |
| VaultWarden                  | 密码管理                                 |
| WireGuard                    | VPN                                      |
| ZeroTier                     | 内网穿透                                 |
| qBittorrent                  | P2P 下载。                               |
| tinyMediaManager             | 影视信息刮削                             |
| 微力同步                     | 替代 Dropbox 、传输照片到 PhotoPrism     |

NAS 是我用得最多的功能，平时冷数据全往里扔，减少心理负担。

用 OpenWrt 做旁路网关实现家庭网络翻墙，体验总体还好。为了让 Unraid 的各项功能正常工作，也需要走旁路网关，所以把 OpenWrt 部署到另一台小主机上了。

我那 17 年 8 G 内存的丐版 Macbook Pro 最近越来越显得力不从心，所以时隔多年之后，重新在虚拟机上用回了 Archlinux ， AUR 依然好用，Linux 依然飞快。把开发环境迁移过去，我可以一边流畅地写代码，一边追忆 good old days ，很开心。

虽然没有摄影的爱好，多年积攒的照片也不是我那基础款的 iCloud 和免费的 Google Photos 能放下的，所以一直在外置硬盘里扔着，很少再看。PhotoPrism 有机器学习的能力，可以通过图像识别给照片打标签，也可以实现人脸识别，不过准确度比较一般，算是勉强可用的图片管理系统，总算让我有动力偶尔翻翻旧照片了。

在调研同步照片到 PhotoPrism 的方法的过程中，如果不是注意到了微力同步，我可能就买 PhotoSync 了，在同步照片这个事上，它已经做得足够好了。然而前者不但可以解决这个场景的需求，还可以替代我那只有三个设备限制的免费 Plan 的 Dropbox ，在增加了一台 iMac 之后，早就已经被退出一台设备再登录另一台弄得心碎了。

关于密码管理，在我的 Linux 时代，用了多年 Keepass ，前几年换到 Mac 一段时间后迁移到了 1Password ，虽然它是体验最好的工具，但是在发现 Bitwarden 已经能达到它百分之八九十的体验后，还是想着能省则省吧，好歹一年也有 35$ 。由于早年管理粗犷，且经过多次迁移，累积了很多需要整理的数据，但是免费版的 Bitwarden 账户弱化了这方面的功能。于是我自建了 Vaultwarden 。

在启用了越来越多的服务后，就不能只局限在内网使用了。有两个选择，一个是内网穿透，一个是 DDNS 。ZeroTier 做内网穿透体验很好。不过最近开通了公网 IP 、配置了 DDNS ，开始用 WireGuard ，暂时把 ZeroTier 当作备用线路了。

服务增加后另一个大问题是记忆的压力。反向代理可以实现通过域名访问各种 Docker 服务，避免记忆一堆端口号。SWAG 集成了 ACME 工具和 NGINX ，可以很方便地自动申请和更新 SSL 证书，提供了大量的反向代理模板，稍加修改就可以通过 HTTPS 访问各种内网服务。由于当前版本的 SWAG 的 ACME 默认并不支持小众的 namesilo ，我只好把域名的 DNS 解析服务迁移到了 Cloudflare ，并通过 Cloudflare-DDNS 自动更新 DNS 记录。

随大流搭了 Adguard Home ，众所周知的目的是 DNS 缓存、防污染和过滤广告，而我暂时还没有什么体会。

Jellyfin 也是随大流搭的。在我看来，媒体中心的作用除了在移动端看电影，就是当机械硬盘或家庭千兆网络无法支撑 Remux 4k 这种动辄 50GB 的高清电影的流畅播放时用来转码的。所以可以视自己的需求取舍。

在媒体中心或者电视上展示 NAS 中电影的海报墙需要刮削工具。桌面版 tinyMediaManager 的体验很好，但是在 Docker 里部署的会有一些我暂时没能解决的问题，而且还显示效果不好、输入中文麻烦。

我很在意硬盘的自动休眠，当然暂时也不打算挂 PT ，目前大部分资源都通过阿里云盘下载，有些优先级不高或者云盘里找不到的也会扔到 qBittorrent 里慢慢下。qBittorrent 是在我这里唯一可用性还可以接受的 BT 下载工具，当然也需要经常更新 trackers 。

为了方便在移动端访问家庭网络里的服务，我用 Heimdall 搭建了一个导航页，有很美观的 UI ，而且对移动端很友好。

最后算笔有意思的账吧，攒这台 All-in-one 软硬件花了大概 7k ，替代 Dropbox Plus 119.88\$/y 、 iCloud 200G 252￥/y 、 Google One 200G 29.99\$/y 、 1Password 35\$/y ，每年省 1.5k ，大约 5 年回本。而如果考虑到省了一台 32G 、 1TB 的 M1 Max ，那真是感觉赚了一个亿……