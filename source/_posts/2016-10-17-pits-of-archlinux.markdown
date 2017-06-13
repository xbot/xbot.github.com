---
layout: post
title: "Archlinux安裝過程中的幾個坑"
date: 2016-10-17 13:14
comments: true
categories: 計算機
tags:
- Archlinux
- Linux
- 操作系統
---

## 安裝方案 ##

[Archboot][1]是比官方鏡像更友好的安裝媒介，此外還有[AUI][2]和[Arch Anywhere][3]，沒有試過。

## 分區 ##

Archboot使用parted處理分區任務。

第一個分區不能從sector 0開始，否則安裝完成後系統玩法啓動，報如下錯誤：

> no operating system found

正確的姿勢：

> (parted) mkpart primary 2048s 512

以上假設第一個分區用來掛載/boot，分配512M。

還需要設置/boot所在的分區可啓動：

> (parted) set 1 boot on

## 啓動引導器 ##

GRUB的兼容性比較好。

如果是syslinux，對於沒有單獨對/boot分區並且根分區使用ext4的情況，會無法啓動，報如下錯誤：

> failed to load ldlinux.c32

此時，應對/boot單獨分區並使用fat格式。

## 圖形界面 ##

安裝X不會連帶安裝顯卡驅動，要單獨安裝，否則啓動圖形界面會黑屏。

在VirtualBox中安裝時，驅動在「virtualbox-guest-utils」。


  [1]: https://mirrors.ustc.edu.cn/archlinux/iso/archboot/latest/
  [2]: https://github.com/helmuthdu/aui
  [3]: https://arch-anywhere.org
