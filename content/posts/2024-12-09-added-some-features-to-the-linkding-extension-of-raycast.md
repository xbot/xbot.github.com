---
title: 给 Raycast 的 linkding 扩展加了点功能
slug: Added some features to the linkding extension of Raycast
date: 2024-12-09 22:11:03+08:00
tags:
- Raycast
- linkding
- 书签管理
- 生产力工具
- 计算机
toc: false
draft: false
---
![2024-12-09-22-28-45-Image1080x1439](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241209222845000-512187e891a5c5f85a38f40b73b1e58d.avif)

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

![2024-12-09-22-36-05-Image1500x948(1)](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241209223605000-e578367ecc02299350c5848e71cc46c5.avif)

![2024-12-09-22-36-54-Image1500x948](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241209223654000-7e977cf5742fcd5adde84afaf1209536.avif)

![2024-12-09-22-37-24-Image1662x1080](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241209223724000-28af3ef3e932537e7b8beee1b3b92dcf.avif)

![2024-12-09-22-37-55-Image1500x948(2)](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/20241209223755000-90d3b2869838a5f80b860da4ba411e22.avif)

在原作者合并 PR 之前，可以下载这个 [repo](https://github.com/xbot/raycast-linkding) 的源码，并执行：

```shell
npm install && npm run dev
```