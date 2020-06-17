---
layout: post
title: "Archlinux安装过程中的几个坑"
slug: pits of archlinux
date: 2016-10-17 13:14:00
comments: true
categories:
- 计算机
tags:
- Archlinux
- Linux
- 操作系统
---

## 安装方案 ##

[Archboot][1]是比官方镜像更友好的安装媒介，此外还有[AUI][2]和[Arch Anywhere][3]，没有试过。

## 分区 ##

Archboot使用parted处理分区任务。

第一个分区不能从sector 0开始，否则安装完成后系统玩法启动，报如下错误：

> no operating system found

正确的姿势：

> (parted) mkpart primary 2048s 512

以上假设第一个分区用来挂载/boot，分配512M。

还需要设置/boot所在的分区可启动：

> (parted) set 1 boot on

## 启动引导器 ##

GRUB的兼容性比较好。

如果是syslinux，对于没有单独对/boot分区并且根分区使用ext4的情况，会无法启动，报如下错误：

> failed to load ldlinux.c32

此时，应对/boot单独分区并使用fat格式。

## 图形界面 ##

安装X不会连带安装显卡驱动，要单独安装，否则启动图形界面会黑屏。

在VirtualBox中安装时，驱动在「virtualbox-guest-utils」。


  [1]: https://mirrors.ustc.edu.cn/archlinux/iso/archboot/latest/
  [2]: https://github.com/helmuthdu/aui
  [3]: https://arch-anywhere.org
