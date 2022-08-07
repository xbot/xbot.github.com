---
title: "Leaderf-folder: Quickly open a subfolder"
slug: "The Leaderf Folder Plugin"
date: 2022-08-07T17:31:50+08:00
categories:
- 计算机
tags:
- 编程
- Vim
- LeaderF
- plugin
---

[LeaderF-folder](https://github.com/xbot/LeaderF-folder) is a plugin for [LeaderF](https://github.com/Yggdroot/LeaderF) that aims to open a subfolder quickly.

![screen cast](https://raw.githubusercontent.com/xbot/LeaderF-folder/main/image/screencast.gif)

## Requirements

- [LeaderF](https://github.com/Yggdroot/LeaderF)
- [fd](https://github.com/sharkdp/fd)
- [Dirbuf](https://github.com/elihunter173/dirbuf.nvim): Optional, can be changed to other file managers.

## Setup

This plugin takes [Dirbuf](https://github.com/elihunter173/dirbuf.nvim) as the default solution to open the chosen subfolder. You can use the following option to customize it:

```vim
let g:Lf_FolderAcceptSelectionCmd = 'Dirbuf'
```

## Usage

```vim
:LeaderfFolder
```

Press `F1` to get more help
