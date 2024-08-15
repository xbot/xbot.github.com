---
title: 突然成了 Up 主
slug: Suddenly Became a Youtuber
date: 2024-08-15T15:46:29+08:00
categories:
  - 青梅煮酒
tags:
  - Calibre
  - YouTuber
toc: false
draft: false
---

早在一年前还在用 Inoreader 的时候就发现 Calibre 抓取某些订阅源之后图片不显示了，后来迁移到 Miniflux ，由于 Calibre 抓取的是它缓存的网页内容，所以这个问题暂时被规避了。现在改用 Readwise Reader 挑拣稍后读的内容，图片的问题又出现了，甚至增加了文章内容为空的情况。

抽时间调试了一下，发现图片不显示是因为 Calibre 在抓取时没有发送 Referer 的 HTTP 报文头，内容为空是因为文章格式较为特殊（比如链接相对正文的比重较大）、导致 Calibre 当成非正文内容过滤掉了。

然后了解了一下 Calibre Recipe 。官方文档比较简单，需要看源码辅助理解，有些问题还需要通过调试找到切入点。终于解决了所有问题，又可以用 Calibre Content Server 的网页端快乐地阅读了。

突然觉得这种比较小的点很适合用短视频的形式表达，所以买了一个月剪映会员，用截图和录屏做了一个 30 秒的视频，旁白用 AI 语音，效果还不错。平时看过一些类似形式做的长视频，TTS 波澜不惊的颜色听多了挺难受，不过短视频就不存在这个问题，观众还没有疲劳就结束了。

<iframe width="560" height="315" src="https://www.youtube.com/embed/uzQpO_G_vPg?si=8FBqc40NyFSyvlXZ" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>