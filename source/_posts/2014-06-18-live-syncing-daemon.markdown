---
layout: post
title: "使用lsyncd同步文件"
date: 2014-06-18 14:03
comments: true
categories: 計算機
tags:
- Linux
- 應用
---

lsyncd全稱“Live Syncing Daemon”，是Linux下的文件自动同步工具，同时支持SSH、rsync的实现方式。相对于rsync+inotify，它速度更快，也更穩定。

對於SSH的方式，需要目標機器中已啟動SSHD，並把源機器上的公鑰加到目標機器root用戶的authorized_keys中，私鑰應放在源機器上運行lsyncd的用戶的.ssh目錄中，密鑰放錯了用戶，會導致無法同步。

然後就是在源機器上創建配置文件，lsyncd的配置文件是個lua腳本：

{% codeblock lang:lua lsyncd.conf %}
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
{% endcodeblock %}

然後啟動lsyncd：

{% codeblock lang:bash %}
sudo lsyncd /etc/lsyncd.conf
{% endcodeblock %}
