---
layout: post
title: "用树苺派实现远程下载"
date: 2015-03-28 19:14:00
comments: true
categories:
- 计算机
tags:
- 树苺派
- Geek
- 智能家居
---

远程用树苺派利用空闲时间下载大文件，需要百度云、aria2和VPS。因为网络运营商给的IP不是真的公网IP，而且免费的动态域名服务不稳定，所以用VPS把树苺派上的端口转发到外网。

## 树苺派

在树苺派上部署aria2下载服务，并发布到VPS。

安装aria2，创建以下配置文件，修改/media/sda1为实际下载目录：

```ini /etc/aria2/aria2.conf
dir=/media/sda1
file-allocation=prealloc
continue=true
log-level=info
#log-level=debug
max-connection-per-server=10
summary-interval=120
daemon=true
enable-rpc=true
rpc-listen-port=6800
rpc-listen-all=true
max-concurrent-downloads=3
save-session=/etc/aria2/save-session.list
input-file=/etc/aria2/save-session.list
log=/media/sda1/aria.log
disable-ipv6=true
disk-cache=25M
timeout=600
retry-wait=30
max-tries=0
user-agent=netdisk;4.4.0.6;PC;PC-Windows;6.2.9200;WindowsBaiduYunGuanJia
```

我的树苺派用Archlinux，创建systemd的服务配置文件：

```ini /etc/systemd/system/aria2c.service
[Unit]
Description=aria2c -- file download manager
After=network.target
 
[Service]
Type=forking
User=%i
WorkingDirectory=%h
Environment=VAR=/var/%i
ExecStart=/usr/bin/aria2c --daemon --enable-rpc --rpc-listen-all --rpc-allow-origin-all -c -D --conf-path=/etc/aria2/aria2.conf
 
[Install]
WantedBy=multi-user.target
```

激活并启动aria2服务。

在NGINX的WWW目录下安装aria2的Web界面：

```bash
git clone https://github.com/binux/yaaw.git
```

发布本地服务到VPS：

```bash
# 发布NGINX
autossh -M 5122 -R 80:localhost:80 myvps.com
 
# 发布aria2
autossh -M 5124 -R 6800:localhost:6800 myvps.com
```

autossh用于保持SSH连接，需要VPS上启动TCP Echo服务。

## VPS

在VPS上启用TCP Echo服务，安装xinetd并修改配置文件：

```lua /etc/xinet.d/echo-stream
service echo
{
        disable         = no
        id              = echo-stream
        type            = INTERNAL
        wait            = no
        socket_type     = stream
        user                    = root
        server                  = /usr/bin/cat
        log_on_failure          += USERID
        flags                   = REUSE
        only_from               = 127.0.0.1
}
```

## 远程主机

在远程主机上配置浏览器的代理规则，对**http://localhost/yaaw**和**http://localhost:6800/jsonrpc**两个URL使用VPS上的VPN或Shadowsocks代理。

安装[Chrome扩展](https://chrome.google.com/webstore/detail/baiduexporter/mjaenbjdjmgolhoafkohbhhbaiedbkno?utm_source=chrome-app-launcher-info-dialog)，然后到百度云盘里设置aria2的RPC地址为“**http://localhost:6800/jsonrpc**”即可。
