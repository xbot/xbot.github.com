---
title: 从 Inoreader 到 Miniflux：寻找理想的 RSS 阅读器之旅
slug: replacing inoreader with miniflux
date: 2024-02-13T21:51:59+08:00
categories:
  - 青梅煮酒
tags:
  - RSS
  - Inoreader
  - Miniflux
toc: false
draft: false
---

在信息爆炸的时代，我们每天都在被各种新闻、文章和更新淹没。作为一个热衷于高效获取信息的读者，我一直在寻找一个理想的 RSS 阅读器，它能够让我快速筛选出感兴趣的内容，同时提供舒适的阅读体验。经过多年的探索，Inoreader 成为了我的首选，它几乎满足了我对 RSS 阅读器的所有期望。然而，最近 Inoreader 的价格调整让我不得不重新审视我的选择。

# 我理想中的 RSS 阅读器

我理想中的 RSS 阅读器应该具备以下几个特点：

1. **关键词过滤**：这让我能够避免浪费时间在不感兴趣的内容上，专注于那些真正有价值的信息。
2. **多种视图支持**：至少包括列表和卡片视图，这样我可以为不同的订阅源选择最合适的视图。例如，对于变形金刚的优惠信息，卡片视图能让我一眼看到感兴趣的型号。
3. **稍后读功能**：RSS 阅读器对我来说主要是快速浏览的工具，对于那些需要深入阅读的内容，我希望能够标记为稍后阅读。
4. **快捷键支持**：快捷键能够极大提高我的阅读效率，让我在浏览时更加得心应手。

# Inoreader 的变迁

Inoreader 在过去几年里几乎完全符合我的需求，即使在前两年涨价后，它仍然允许老用户以原价续费，这让我感到非常满意。然而，最近我收到了一封邮件，通知我年费直接涨了近一倍。这让我不得不开始考虑转移到私有云部署的 RSS 订阅工具。

![2024-02-13-21-31-08-Screenshot_20240207-012352_Original](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-02-13-21-31-08-Screenshot_20240207-012352_Original.jpeg)

# 试用 FreshRSS 和 Miniflux

在尝试了 FreshRSS 和 Miniflux 之后，我发现 FreshRSS 存在过滤规则保存后为空的问题，另外全文抓取也需要更多更复杂的配置，不如 Miniflux 稳定和简单。最终，我决定保留 Miniflux。

# Miniflux 的优势

Miniflux 是用 Go 语言实现的，拥有极简的 WebUI，占用系统资源较少，抓取能力出色。它支持根据订阅源的更新频率定制抓取时间间隔，还支持多种 Web 服务的集成，如我在用的 Readwise Reader。我选择使用 Docker 将 Miniflux 部署到 NAS 中，经过几天的试用，稳定性令人满意。

# Miniflux 的不足

尽管 Miniflux 在功能上满足了我的需求，但其 Web 界面过于简洁，不太适合我快速浏览大量订阅内容的工作流。为了解决这个问题，我在 macOS 上配合 NetNewsWire 使用 Miniflux，不过这个阅读器缺少多种视图等功能，只能算是差强人意。

# 未来的选择

如果没有意外，我计划在本月底 Inoreader 订阅到期后，正式放弃使用它，转而全面拥抱 Miniflux。虽然这个决定伴随着对 Inoreader 的不舍，但我相信 Miniflux 能够为我提供一个更加经济、灵活且高效的 RSS 阅读体验。

# 结语

在这个不断变化的数字世界中，找到适合自己的工具至关重要。Miniflux 虽然不是完美的解决方案，但它的灵活性和开源特性让我看到了无限的可能性。我期待着与 Miniflux 一起成长，探索更多高效阅读的新方式。