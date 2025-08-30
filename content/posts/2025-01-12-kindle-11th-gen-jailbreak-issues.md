---
title: Kindle 11 代越狱过程中的一些问题
slug: kindle 11th gen jailbreak issues
date: 2025-01-12T21:31:26+08:00
tags:
  - 青梅煮酒
  - Kindle
toc: false
draft: false
---

前几天给 KO2 越狱十分顺滑，没想到昨天给抹茶做的时候竟然每一步都不顺利。

![2025-01-12-21-37-19-IMG_3809](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20250112213719000-1e5a6249f9af0125255eaaa7f3dbf277.avif)

首先越狱一次没成功，安装 Update Hotfix 并重启后没有出现 Run Hotfix 项。然后重新执行越狱步骤后才成功。

随后安装 KUAL 时提示“应用程序错误 所选应用程序无法启动”，不过安装后 KUAL 可用，就没有管它，算是有惊无险。

最后，KOReader 安装后无法启动，没有任何提示，在他的目录中的 crash.log 里找到错误信息说是 reader.lua 文件找不到，但是对应的文件其实是存在的。KOReader 的 GitHub 仓库中有人反馈类似的问题，虽然跟我的情况不完全相同，但是试了一下，是行得通的，安装最新的 [nightly build](https://fw.notmarek.com/khf/koreader/) 版本就行了。

大概是因为新版的 Kindle 发布较晚，越狱程序和 KOReader 没有经过充分的测试，毕竟最初用的 KOReader 稳定版是几个月前发布的。