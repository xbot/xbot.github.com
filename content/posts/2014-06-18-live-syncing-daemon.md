---
layout: post
title: "使用lsyncd同步文件"
date: 2014-06-18 14:03:00
comments: true
categories:
- 计算机
tags:
- Linux
- 应用
---

lsyncd全称“Live Syncing Daemon”，是Linux下的文件自动同步工具，同时支持SSH、rsync的实现方式。相对于rsync+inotify，它速度更快，也更稳定。

对于SSH的方式，需要目标机器中已启动SSHD，并把源机器上的公钥加到目标机器root用户的authorized_keys中，私钥应放在源机器上运行lsyncd的用户的.ssh目录中，密钥放错了用户，会导致无法同步。

然后就是在源机器上创建配置文件，lsyncd的配置文件是个lua脚本：

```lua
settings{
    pidfile = "/var/log/lsyncd/lsyncd.pid",
    logfile = "/var/log/lsyncd/lsyncd.log",
    statusFile = "/var/log/lsyncd/lsyncd-status.log",
    statusInterval = 1,
    maxDelays = 1,
    -- nodaemon = true,
}

sync{
    default.rsyncssh,
    source = "/home/monk/workspace",
    host = "192.168.1.3",
    targetdir = "/var/www/workspace",
    exclude={ ".*", "*.tmp" },
    rsync = {
        compress = false,
        _extra = {"--bwlimit=50000"},
    }
}
```

然后启动lsyncd：

```bash
sudo lsyncd /etc/lsyncd.conf
```
