---
layout: post
title: "Linux下做Mac OSX安装U盘的步骤"
slug: create mac bootable usb stick on linux
date: 2016-03-08 12:22:00
comments: true
categories:
- 计算机
tags:
- archlinux
- mac
---

假设U盘对应/dev/sdb1、OSX安装包是osx.dmg。整个过程就是提取出一系列文件，然后复制到U盘里。

```bash
# 格式化U盘为hfs+文件系统
sudo mkfs.hfsplus -v EICaptianInstall /dev/sdb1
 
# 提取和挂载第一个光盘镜像
dmg2img -p 5 osx.dmg osx.img
mkdir raw && sudo mount -o loop osx.img raw
 
# 提取和挂载第二个光盘镜像
dmg2img -p 5 raw/Install\ OS\ X\ El\ Capitan.app/Contents/SharedSupport/InstallESD.dmg InstallESD.img
mkdir esd && sudo mount -o loop InstallESD.img esd
 
# 提取和挂载第三个光盘镜像
dmg2img -p 4 esd/BaseSystem.dmg BaseSystem.img
mkdir base && sudo mount -o loop BaseSystem.img base
 
# 挂载U盘
mkdir usb && sudo mount /dev/sdb1 usb
 
# 复制一系列文件
sudo cp -r base/* usb/
sudo rm usb/System/Installation/Packages
sudo cp -r esd/Packages usb/System/Installation/
sudo cp esd/BaseSystem.* usb/
```

注意dmg2img的参数-p表示提取dmg的第几个分区，似乎不同的OSX版本提取哪个分区也不一样，判断方法就是先执行`dmg2img osx.dmg`，看哪个分区提取得最慢就选哪个。
