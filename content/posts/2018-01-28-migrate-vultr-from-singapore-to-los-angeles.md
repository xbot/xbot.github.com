---
title: 从Vultr新加坡迁移到洛杉矶
slug: migrate vultr from singapore to los angeles
date: 2018-01-28 14:15:57
tags:
- 笔记
- 计算机
---
Vultr新加坡节点挂了两天了，不能翻墙简直什么都干不了。早上用CloudSpeed看了下，洛杉矶机房的响应速度还不错，就迁移了过去。

## ShadowsocksR
这个很简单，用[一键安装脚本](https://github.com/91yun/shadowsocks_install)。

## 网络加速
用BBR，[一键安装脚本](https://teddysun.com/489.html)。

## Tiny Tiny RSS
先按照之前的[笔记](/post/ttrss/)安装ttrss。

然后重建一个空的ttrss的数据库：

```bash
docker exec f6d92ad8efba /usr/bin/psql -c "drop database ttrss"
docker exec f6d92ad8efba /usr/bin/psql -c "create database ttrss"
```

把数据库的备份复制到容器里并导入：

```bash
docker cp ttrss.sql f6d92ad8efba:/tmp/ttrss.sql
docker exec f6d92ad8efba /usr/bin/psql -d ttrss -f /tmp/ttrss.sql postgres
```

以root身份登录容器并删除备份文件：

```bash
docker exec -it --user root f6d92ad8efba /bin/sh
rm /tmp/ttrss.sql
```

