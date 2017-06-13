---
layout: post
title: "在Archlinux ARM上使用DS18B20溫度傳感器"
date: 2015-05-25 15:30
comments: true
categories: 計算機
tags:
- Archlinux
- 樹苺派
- 傳感器
- geek
---

線路圖：

{% img http://pic.yupoo.com/leninlee/EG46yU99/medish.jpg %}

修改/boot/config.txt，啟用w1內核模塊。配置文件中有兩部分關於w1的內容，一個只使用一個GPIO引腳，需要自行添加上拉電阻，另一個使用一個額外的GPIO引腳作上拉。

使用Python模塊w1thermsensor讀數，用法見[這裡](https://github.com/timofurrer/w1thermsensor)。
