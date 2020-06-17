---
layout: post
title: "ZSH下新安装的程序无法自动补全的解决方法"
slug: how to auto complete newly installed command in zsh
date: 2014-11-12 22:15:00
comments: true
categories:
- 计算机
tags:
- zsh
- linux
---

Zsh默认开启了对PATH变量的缓存，这是导致新安装的程序无法立即使用自动补全的原因。

其实只要PATH变量不太复杂，安装的程序不太多，完全没必要开启缓存，实际上我把缓存关掉后完全没有感觉到补全的速度有什么变化。

方法如下，在.zshrc中增加一行：

```bash
zstyle ':completion:*' rehash true
```

也可以在必要的时间手工执行命令**rehash**，也是个临时解决方法。
