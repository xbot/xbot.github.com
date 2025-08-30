---
title: 改变 Vim 模式时自动切换输入法的方法
slug: auto switch input method while changing vim mode
date: 2020-02-19 21:48:15
tags:
- 最佳实践
- vim
- vscode
- 计算机
---

VS Code的Vim插件有个很贴心的功能，可以在切换到普通模式时自动切换到英文输入法，而当切换回插入模式时再换到此前的输入法。

设置的方法很简单。先安装[im-select](https://github.com/daipeihust/im-select)，然后配置如下（macOS）：

![2020-02-19-21-59-02-510Ak5](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20200219215902000-1bbeb5210a8d458f4edf04b617e0b0ea.avif)

当然，在Vim里也可以利用im-select实现这个功能。
