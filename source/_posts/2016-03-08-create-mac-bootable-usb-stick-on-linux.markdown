---
layout: post
title: "Linux下做Mac OSX安裝U盤的步驟"
date: 2016-03-08 12:22
comments: true
categories: 計算機
tags:
- archlinux
- mac
---

假設U盤對應/dev/sdb1、OSX安裝包是osx.dmg。整個過程就是提取出一系列文件，然後複制到U盤裏。

{% codeblock lang:bash %}
# 格式化U盤爲hfs+文件系統
sudo mkfs.hfsplus -v EICaptianInstall /dev/sdb1
 
# 提取和挂載第一個光盤鏡像
dmg2img -p 5 osx.dmg osx.img
mkdir raw && sudo mount -o loop osx.img raw
 
# 提取和挂載第二個光盤鏡像
dmg2img -p 5 raw/Install\ OS\ X\ El\ Capitan.app/Contents/SharedSupport/InstallESD.dmg InstallESD.img
mkdir esd && sudo mount -o loop InstallESD.img esd
 
# 提取和挂載第三個光盤鏡像
dmg2img -p 4 esd/BaseSystem.dmg BaseSystem.img
mkdir base && sudo mount -o loop BaseSystem.img base
 
# 挂載U盤
mkdir usb && sudo mount /dev/sdb1 usb
 
# 複制一系列文件
sudo cp -r base/* usb/
sudo rm usb/System/Installation/Packages
sudo cp -r esd/Packages usb/System/Installation/
sudo cp esd/BaseSystem.* usb/
{% endcodeblock %}

注意dmg2img的參數-p表示提取dmg的第幾個分區，似乎不同的OSX版本提取哪個分區也不一樣，判斷方法就是先執行`dmg2img osx.dmg`，看哪個分區提取得最慢就選哪個。
