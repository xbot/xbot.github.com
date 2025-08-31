---
title: 用vim-workspace换掉了session.vim
slug: replace session vim with vim workspace
date: 2017-07-19 17:47:30
tags:
- Vim
- 最佳实践
- 计算机
---

用session.vim一直有几个痛点。一是保存会话后，退出时仍然会提示保存。二是会话会记录vimrc，恢复会话后用的仍然是旧的配置。

vim-workspace没有这些问题，而且实现了更多功能。

