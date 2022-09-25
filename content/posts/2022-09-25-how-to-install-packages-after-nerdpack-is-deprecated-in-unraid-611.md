---
title: "Unraid 6.11 弃用 NerdPack 的解决办法"
slug: "How to install packages after NerdPack is deprecated in Unraid 6.11"
date: 2022-09-25T21:29:41+08:00
categories: ["计算机"]
tags:
- NAS
- homelab
- Unraid
toc: false
---

Unraid 6.11 的 release note 没提弃用 NerdPack ，浪费了我很多时间。😞

当前版本的 Unraid 是基于 Slackware 15.0 的，可以手动从以下站点下载软件包手动安装：

- https://slackware.pkgs.org/15.0/slackware-x86_64/
- https://slackonly.com/pub/packages/15.0-x86_64/

把下载的软件包放到运行中的 Unraid 的 `/boot/extra` 目录（不存在就手动创建）下，重启系统后自动安装或者通过 `installpkg` 命令手动安装。例如：

```bash
installpkg vim-8.2.4256-x86_64-1.txz
```

更简单的方法是使用 [un-get](https://github.com/ich777/un-get) ：

```bash
un-get search vim
un-get install python3 python-pip python-setuptools
un-get remove vim
un-get update
un-get cleanup
un-get upgrade
```

这个命令目前不解决依赖关系，而且存在部分包无法下载或找不到的现象，还是需要手动干预。

作者没有在项目主页写安装说明，而是写在 [Reddit 帖子](https://www.reddit.com/r/unRAID/comments/wy9nft/unget_a_simple_command_line_tool_to_install/)里了。