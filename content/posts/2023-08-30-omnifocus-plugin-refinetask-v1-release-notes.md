---
title: "OmniFocus 插件 refine-task v1.0 小记"
slug: "OmniFocus Plugin refine-task v1 release notes"
date: 2023-08-30T22:05:59+08:00
categories: ["青梅煮酒"]
tags:
- omnifocus
- 效率工具
toc: false
---

[refine-task](https://github.com/xbot/omnifocus-plugin-refine-task) 的功能非常简单，它为选中的任务打开一个输入框，将用户输入的内容作为新的任务内容保存，原来的任务内容会被挪到备注里：

<video controls autoplay loop width="100%">
  <source src="https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-08-30-22-04-08-Kapture 2023-08-30 at 19.33.05.mp4" type="video/mp4">
  Your browser does not support the video tag.
</video>

我用快捷指令“[收集 Tweet 到 OmniFocus](https://www.icloud.com/shortcuts/dd3a5d430a434d39aff4e3092e7abfee)”收集 tweets ，但是因为 tweet 内容较长，不方便 review 的时候快速了解为什么收集这条内容，所以我用快捷指令“[提炼 OmniFocus](https://www.icloud.com/shortcuts/899467b735304519aa4b26b8c7756433)”利用 ChatGPT 提炼待办事项并复制到剪贴板，接着这个快捷指令会调用 refine-task 把条目原文移动到备注里并打开输入框由用户粘贴或者输入待办事项。