---
title: 给 Raycast 的 linkding 扩展加了点功能
slug: Added some features to the linkding extension of Raycast
date: 2024-12-09T22:11:03+08:00
categories:
  - 计算机
tags:
  - Raycast
  - linkding
  - 书签管理
  - 生产力工具
toc: false
draft: false
---
![2024-12-09-22-28-45-Image1080x1439](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-09-22-28-45-Image%201080x1439.jpeg)

上周六风很大，被迫放弃去大运河骑车，窝在家里改 Raycast 的 linkding 扩展。

Raycast 是一个 macOS 上的应用启动器和效率工具，类似 Spotlight 和 Alfred ，可以快速访问各类应用、文件等。linkding 是一个可以自己部署的书签管理工具，可以替代浏览器的书签管理功能，同时提供更强大、更灵活的特性。Raycast 的 linkding 扩展可以对存储在 linkding 中的书签做快速检索和管理的操作。

这个扩展原本的界面和功能比较简陋，我给它加了以下内容：

- 可选择是否在列表中显示网站的图标。
- 可选择是否在列表中显示副标题。
- 可选择副标题区域显示网址的描述或者备注。
- 增加编辑书签的功能。
- 创建和编辑书签时可以给书签打 tag 。
- 删除书签和 linkding 账户时弹出确认对话框，防止误删。
- 增加在浏览器中打开 linkding 书签查看和编辑页面的功能。

![2024-12-09-22-36-05-Image1500x948(1)](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-09-22-36-05-Image%201500x948%20(1).jpeg)

![2024-12-09-22-36-54-Image1500x948](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-09-22-36-54-Image%201500x948.jpeg)

![2024-12-09-22-37-24-Image1662x1080](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-09-22-37-24-Image%201662x1080.jpeg)

![2024-12-09-22-37-55-Image1500x948(2)](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2024-12-09-22-37-55-Image%201500x948%20(2).jpeg)

在原作者合并 PR 之前，可以 fork 这个 [repo](https://github.com/xbot/raycast-linkding) ，并执行：

```shell
npm install && npm run dev
```