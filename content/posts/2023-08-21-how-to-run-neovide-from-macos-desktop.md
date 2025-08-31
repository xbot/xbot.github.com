---
title: 从 macOS 桌面运行 Neovide 的方法
slug: How to Run Neovide from macOS Desktop
date: 2023-08-21 17:07:48+08:00
tags:
- 计算机
- mac
- NeoVim
- Vim
toc: false
---

截至当前版本（0.11.1），Neovide 在 macOS 下只能从命令行启动，为方便起见，有时候我希望通过 RayCast 或者 Dashboard 启动它，或者通过右键菜单“Open with”用它直接打开选中的文件。

方法是通过 Automator 创建一个“Application”类型的新文档，添加一个“Run AppleScript”的 Action 。然后填入下面的内容：

{{< gist xbot a2f1d38e8eb02665ef7cf9ba082ccd12 >}}

最后将文档保存到 Application 目录，命名为“Neovide”。
