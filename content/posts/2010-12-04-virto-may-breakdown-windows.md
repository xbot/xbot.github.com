---
layout: post
title: virtio可能导致windows蓝屏
slug: virto may breakdown windows
date: 2010-12-04 00:00:00
tags:
- kvm
- Windows
- 虚拟技术
- 计算机
status: publish
published: true
comments: true
meta:
  _edit_last: '1'
  views: '731'
  _wp_old_slug: ''
---
前段时间突然发现kvm中的windows在启动时蓝屏，开始以为是kvm新版本的bug，后来发现如果不启用虚拟机硬盘的virtio就不会出现蓝屏。

于是下载了最新版的virtio，然而更新后，蓝屏的问题并没有解决，所以目前只能暂时不使用virtio。不过网卡的virtio是没有问题的。
