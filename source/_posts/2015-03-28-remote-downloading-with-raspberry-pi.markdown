---
layout: post
title: "用樹苺派實現遠程下載"
date: 2015-03-28 19:14
comments: true
categories: 計算機
tags:
- 樹苺派
- Geek
- 智能家居
---

遠程用樹苺派利用空閒時間下載大文件，需要百度雲、aria2和VPS。因為網絡運營商給的IP不是真的公網IP，而且免費的動態域名服務不穩定，所以用VPS把樹苺派上的端口轉發到外網。

## 樹苺派

在樹苺派上部署aria2下載服務，並發佈到VPS。

安裝aria2，創建以下配置文件，修改/media/sda1為實際下載目錄：

{% codeblock lang:ini /etc/aria2/aria2.conf %}
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
{% endcodeblock %}

我的樹苺派用Archlinux，創建systemd的服務配置文件：

{% codeblock lang:ini /etc/systemd/system/aria2c.service %}
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
{% endcodeblock %}

激活並啟動aria2服務。

在NGINX的WWW目錄下安裝aria2的Web界面：

{% codeblock lang:bash %}
git clone https://github.com/binux/yaaw.git
{% endcodeblock %}

發佈本地服務到VPS：

{% codeblock lang:bash %}
# 發佈NGINX
autossh -M 5122 -R 80:localhost:80 myvps.com
 
# 發佈aria2
autossh -M 5124 -R 6800:localhost:6800 myvps.com
{% endcodeblock %}

autossh用於保持SSH連接，需要VPS上啟動TCP Echo服務。

## VPS

在VPS上啟用TCP Echo服務，安裝xinetd並修改配置文件：

{% codeblock lang:lua /etc/xinet.d/echo-stream %}
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
{% endcodeblock %}

## 遠程主機

在遠程主機上配置瀏覽器的代理規則，對**http://localhost/yaaw**和**http://localhost:6800/jsonrpc**兩個URL使用VPS上的VPN或Shadowsocks代理。

安裝[Chrome擴展](https://chrome.google.com/webstore/detail/baiduexporter/mjaenbjdjmgolhoafkohbhhbaiedbkno?utm_source=chrome-app-launcher-info-dialog)，然後到百度雲盤裡設置aria2的RPC地址為“**http://localhost:6800/jsonrpc**”即可。
