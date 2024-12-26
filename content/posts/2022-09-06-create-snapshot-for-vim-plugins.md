---
title: 给 Vim 插件做快照
slug: Create snapshot for vim plugins
date: 2022-09-06 23:28:26+08:00
tags:
- Vim
- 计算机
toc: false
---

我的 Vim 插件数快到 150 了，弊端也越来越明显。

最大的问题是稳定性，经常批量更新后发现某个功能不工作了，尤其是大部分问题只有在用到的时候才发现，非常影响效率。

我用的插件管理工具是 [vim-plug](https://github.com/junegunn/vim-plug) ，虽然有回滚到上一次的功能，但是我手比较欠，经常频繁更新，往往发现一个问题时已经更新了几次了，所以这个功能对我来说不是很适用。

好在它提供了做快照的命令 `:PlugSnapshot` ，其实就是把当前所有插件的 Git 哈希值输出到一个 Vim 脚本里，恢复快照的时候只要执行这个脚本就行了。

为了方便使用，创建一个自定义命令：

```vim
command! SnapshotPlugins PlugSnapshot ~/.vim/plugin.lock
```

然后把生成的 `plugin.lock` 文件加入 Git 仓库，必要的时候可以利用 `git bisect` 快速找到问题出处。

恢复的命令如下：

```bash
vim -S plugin.lock
```