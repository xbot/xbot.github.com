---
title: MacOS下自动切换Karabiner Elements配置
slug: auto switch keyboard layouts in macos
date: 2017-03-20 13:14:18
tags:
- mac
- 计算机
---

Karabiner Elements不支持对不同的键盘自动使用不同的配置，所以本文通过监听USB键盘插拔事件实现自动切换。

## 依赖：

- Keyboard Maestro
- php

## 脚本

Karabiner Elements会监听配置文件`~/.config/karabiner/karabiner.json`，如果有变动，会自动重新加载。

切换配置的脚本在[这里](https://github.com/xbot/shell/blob/master/karabiner-elements-profile-switcher.php)。

## Keyboard Maestro

键盘插入事件：

![](https://ww3.sinaimg.cn/large/006tNc79ly1fdt8iy3ydmj30cq0b6q3z.jpg)

键盘拔出事件：

![](https://ww2.sinaimg.cn/large/006tNc79ly1fdt8jz1aspj30cn0b1my6.jpg)
