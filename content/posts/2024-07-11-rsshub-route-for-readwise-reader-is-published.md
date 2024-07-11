---
title: 打通稍后读工作流：Readwise Reader 的 RSSHub 路由发布
slug: rsshub route for readwise reader is published
date: 2024-07-11T17:33:26+08:00
categories:
  - 青梅煮酒
tags:
  - RSS
  - 最佳实践
  - RSSHub
  - Miniflux
  - Inoreader
  - Calibre
  - 阅读
toc: false
draft: false
---
本文聊聊在从 Inoreader 迁移到 Miniflux 后，我是怎样重建我的阅读工作流的。

## 原来的阅读工作流

![2024-07-11-17-49-10-SoWkIImgAStDuL8ioKZDJLKepaujISwerCqlBSd9p4lb0ijpSLczR9ZrOd-xWcTUVb5gOafgaSABTOjUpcXL20eEjkQoUZfd1PBKo_QUf-jtlfpJy69Pu_CLlKydzBrOl_lvh9PdqzgVhrxmh7Foindpdorenz07L0AHWIXysZ7znS8L4CFWMe6opzVkiuOxo3an9pEfA5NXMTVaf_tJa0o3A0GQ2N7eirqJ8FR1TJZdvvMa](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-07-11-17-49-10-SoWkIImgAStDuL8ioKZDJLKepaujISwerCqlBSd9p4lb0ijpSLczR9ZrOd-xWcTUVb5gOafgaSABTOjUpcXL20eEjkQoUZfd1PBKo_QUf-jtlfpJy69Pu_CLlKydzBrOl_lvh9PdqzgVhrxmh7Foindpdorenz07L0AHWIXysZ7znS8L4CFWMe6opzVkiuOxo3an9pEfA5NXMTVaf_tJa0o3A0GQ2N7eirqJ8FR1TJZdvvMa.png)

在用过的 RSS 阅读器里，Inoreader 最符合我的阅读习惯。它有以下优点：

- **文章去重和过滤**：由于订阅的资讯较多，这个功能能帮我节省很多时间。
- **接近 Vim 风格的快捷键**：Vim 用户表示极度舒适。
- **多种布局**：为不同的订阅源指定最合适的布局能极大提高阅读效率。
- **支持把标签输出为订阅源**：方便通过 Calibre 订阅和抓取。

我在 Inoreader 里只做快速浏览，对需要阅读的文章打星标，随后集中对星标文章打标签。然后在 Calibre 里抓取指定标签的文章并生成电子书，既可以在 Calibre Content Server 的网页端阅读，也可以发送到 Kindle 离线阅读。

## 电脑上的阅读体验

在电脑上，我一直钟爱使用 Calibre Content Server 的网页端进行阅读。它具备以下特点：

- **自适应多栏布局**：针对高分屏的优化，使得阅读体验更加舒适。
- **卡片式翻页**：与传统的滚动式翻页相比，卡片式翻页在提高阅读效率的同时，也更加护眼。
- **自定义排版样式**：可以根据自己的喜好调整阅读界面，打造个性化的阅读环境。

![2024-07-11-18-06-02-IMG_2332](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-07-11-18-06-02-IMG_2332.jpeg)

## 从 Inoreader 到 Miniflux 的转变

后来，Inoreader 的价格涨了很多，我尝试自建 Miniflux ，效果意外地好，但它有一个明显的局限性：只支持打星标，不支持打标签。这意味着我无法像使用 Inoreader 那样，通过标签对文章进行筛选和分类。

## Readwise Reader 的优势

不过 Miniflux 支持集成 Readwise Reader 。它不仅支持标签功能，还提供了列表接口。这让我意识到，如果能够将 Readwise Reader 的文档输出为订阅源，那么我的稍后读工作流将更加完善。

## RSSHub 路由发布

为了实现这一目标，我为 RSSHub 添加了一个路由，它可以将 Readwise Reader 的文档转换成订阅源。这样一来，我就可以在 Calibre Content Server 中愉快地阅读那些稍后阅读的文章了。

![2024-07-11-18-08-00-mac_20240710163856](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-07-11-18-08-00-mac_20240710163856.png)

新的流程是：

![2024-07-11-18-07-04-SoWkIImgAStDuL8ioKZDJLKepaujISwerCqlBSd9p4lb0ijpMRricFLYVxk2RsPUPbfEQON2YtMBNiveLGWA3hRcidewPmL2vcLxpzFrEr_EQNYnhF7vYjwd4_fUhD_zV5RBisdjprSlUDOv-TcC-SyMj0ErGHWGyHVjgvvshNWweUVPsgLFEpOe1AKcffHd5gU26Qb544lGHK5sFTyz4suoqBJdQ_TPmrmBSfr7QPB2YtsJ](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-07-11-18-07-04-SoWkIImgAStDuL8ioKZDJLKepaujISwerCqlBSd9p4lb0ijpMRricFLYVxk2RsPUPbfEQON2YtMBNiveLGWA3hRcidewPmL2vcLxpzFrEr_EQNYnhF7vYjwd4_fUhD_zV5RBisdjprSlUDOv-TcC-SyMj0ErGHWGyHVjgvvshNWweUVPsgLFEpOe1AKcffHd5gU26Qb544lGHK5sFTyz4suoqBJdQ_TPmrmBSfr7QPB2YtsJ.png)

该路由的详细用法可以参考以下文档：

https://docs.rsshub.app/zh/routes/reading#readwise