---
title: Miniflux 列表最后一页读后自动跳转到未读列表
slug: auto jump to the unread page in miniflux
date: 2025-08-08T15:18:08+08:00
tags:
  - 青梅煮酒
  - Miniflux
  - AI
toc: false
draft: false
---
Miniflux 的任意列表页在对最后一页标记所有项目已读后会刷新页面，往往需要手动跳转到默认未读列表。

分别让 Gemini CLI 和 Claude Code 利用 Miniflux 的自定义 Javascript 功能写段代码实现自动跳转，在读取和分析了 Miniflux 代码且没有给出具体思路的情况下，两者的表现都不是很理想，都经历了多次失败，不过最终都能解决问题。

整体上 Claude Code 解决问题的路径更短一些，比 Gemini CLI 更早地找到了正确的思路。不过在把同样的逻辑从按钮扩展到快捷键时，前者表现出比后者更差的全局观。而 Gemini CLI 在态度上胜出，全程一直在道歉。

<video controls autoplay loop width="100%">
  <source src="https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2025-08-08-15-37-53-boost-miniflux.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>