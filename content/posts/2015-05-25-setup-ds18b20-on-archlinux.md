---
layout: post
title: "在Archlinux ARM上使用DS18B20温度传感器"
date: 2015-05-25 15:30:00
comments: true
categories:
- 计算机
tags:
- Archlinux
- 树苺派
- 传感器
- geek
---

线路图：

![](http://pic.yupoo.com/leninlee/EG46yU99/medish.jpg)

修改/boot/config.txt，启用w1内核模块。配置文件中有两部分关于w1的内容，一个只使用一个GPIO引脚，需要自行添加上拉电阻，另一个使用一个额外的GPIO引脚作上拉。

使用Python模块w1thermsensor读数，用法见[这里](https://github.com/timofurrer/w1thermsensor)。
