---
title: "从 Logseq 迁移到 Obsidian"
slug: "migrate-logseq-to-obsidian"
date: 2026-01-10T12:08:01+08:00
draft: false
tags:
  - 青梅煮酒
  - Logseq
  - Obsidian
---
还是从 Logseq 迁移到了 Obsidian 。

Logseq 的 Journal 太好用了，想写点什么打开就写，不用想标题、分类、目录，写完打几个标签就行，没有内耗。但是 Obsidian 有更好的用户界面和插件生态，Logseq 在这方面逊色不少。

不过 Obsidian 的最小单位是基于文件的，不是基于块，无法像 Logseq 那样用 Journal 做为入口。所以我用的是 Unique Note 。

用 sercxanto/logseq_to_obsidian 做的迁移，整体上没什么问题，只是对 Calibre 导出的高亮笔记处理得不好，会丢失章节标题和定位链接。然后用 OpenCode + MiniMax m2.1 微调迁移后的笔记，主要是处理元数据。
