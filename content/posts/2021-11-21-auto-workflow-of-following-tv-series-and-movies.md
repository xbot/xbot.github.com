---
title: 自动追剧的工作流
slug: Auto Workflow of Following Tv Series and Movies
date: 2021-11-21 17:16:15+08:00
tags:
- NAS
- 最佳实践
- 青梅煮酒
---

在 NAS 上搭了个自动追剧、追影的工作流，体验非常好。

![自动追剧流程](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2021-11-21-17-24-06-自动追剧流程.svg)

只需要把想看的美剧或者电影添加到监控列表里，就可以在资源出现之后自动下载并通知到手机或电脑，全程无值守，回家打开电视就能看。

用到这些工具：

- ~~**Jackett**: BT 索引工具，负责搜索资源。~~
- **Prowlarr**: Jackett 的平替，和 Radarr/Sonarr 同步 indexer 更方便，对 torrent 网站的限流机制处理得更好。
- **Sonarr**: 电视剧管理工具，负责管理追剧列表、调用 Jackett 搜索、资源监控、下载调度。
- **Radarr**: 电影管理工具，功能同 Sonarr 。
- **qBittorrent**: BT 下载工具。
- **ChineseSubFinder**: 字幕搜索工具。
- **Jellyfin**: 媒体中心，负责影视剧转码、播放、管理。
- **Telegram**: 接收通知。
- **nzb360**: Android APP ， Sonarr 、 Radarr 、 qBittorrent 的客户端。
- **LunaSea**: iOS 的 nzb360 平替，功能和用户体验稍逊后者。

Jackett 是 BT 资源索引工具，可以对添加到索引列表中的 BT 资源站点做集中搜索。

![2021-11-21-17-50-31-jackett-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121175031000-cc4a8baea87ccf303b9b558bc0cb1818.avif)

在 Jackett 因某个 indexer 由于其网站的限流措施而响应失败导致 Radarr 无法搜索且无法修改 indexer 配置后，我改用了 Prowlarr 。它不仅解决了之前遇到的问题，还可以自动同步 indexer 配置到 Radarr 和 Sonarr，并且它们的 UI 也十分统一。

Sonarr 专门用来管理剧集，与 Jackett 集成后可以在 Sonarr 的页面上搜索并添加到监控列表。剧集更新后会自动调用下载服务下载，随后导入并刮削元数据。特别地，如果发现有更清晰的资源，它可以自动下载并替换现有的资源。

![2021-11-21-17-53-26-sonarr-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121175326000-0850ef73fa541cb54341bd5ed9abe5be.avif)

Radarr 的功能和 Sonarr 几乎一样，只不过它是用来管理电影的。

![2021-11-21-18-01-00-radarr-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121180100000-2d14e0026bc4f8486ef59e4a6a1477f2.avif)

qBittorrent 是下载工具。

![2021-11-21-18-01-30-qbittorrent-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121180130000-6c91b59364ed8377a1621e0379b016ac.avif)

Jellyfin 是媒体中心，实现对影视资源的管理、转码、播放功能。在播放终端解码能力弱或者网络不够好的场景下比较有用。如果电视的播放器（例如 Infuse 7）刷新海报墙比较慢，Jellyfin 会因为是持续运行的服务而有更好的体验。

![2021-11-21-18-02-23-jellyfin-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121180223000-4607a18454a9e91693b740c207f95dd8.avif)

这里还需要用到 ChineseSubFinder ，它会监视媒体目录并自动下载合适的字幕。

![2021-11-21-18-09-08-chinesesubfinder-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121180908000-b35b581c09bad460a5fd1d7ae63a605a.avif)

当资源被添加、删除或下载时，通过 Telegram 发送通知到手机或者电脑。

![2021-11-21-18-10-40-notice-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181040000-ced8a0d67aa0e1e8a6446cf4b0a4f99c.avif)

![2021-11-21-18-12-18-notice-02](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181218000-3945959d11ab6f29ae0bac5b61d71f25.avif)

nzb360 是 Sonarr 、 Radarr 、 qBittorrent 的 Android 客户端。UI 很漂亮，使用体验也非常好。还可以添加 Jellyfin 等系统的 Web UI ，实现对整个工作流的一站式访问。

![2021-11-21-18-12-43-nzb360-sonarr-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181243000-d5dff3c7194d517f8414fcdb43ac9b0e.avif)

![2021-11-21-18-15-55-nzb360-radarr-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181555000-89cfa7564a2044693f8f9c9aca9b485d.avif)

![2021-11-21-18-16-13-nzb360-calendar-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181613000-c23968bc99ab7ebee0ddb5264648e974.avif)

![2021-11-21-18-16-29-nzb360-qbittorrent-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181629000-55ff7b84974dc5b23bcfe32bdc4862bd.avif)

![2021-11-21-18-16-42-nzb360-jellyfin-01](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20211121181642000-64c4669f71214ef7aa20e6f43964ec46.avif)

在 iOS 上我用 LunaSea 作为 nzb360 的平替，但是在功能和用户体验上要稍逊于后者，作为 Android 手机不在手边时的临时替代品。
