---
layout: post
title: "用樹苺派做家庭監控"
date: 2015-04-07 10:56
comments: true
categories: 計算機
tags:
- 樹苺派
- 智能家居
- Geek
---

用樹苺派做視頻監控，當視野內有物體移動時，自動拍照、錄視頻、同步到遠程主機，並提醒到遠程電腦和手機。

## 用Motion做視頻監控

安裝Motion，修改幾項必要的配置：

{% codeblock lang:ini /etc/motion/motion.conf %}
# 照片和視頻存儲路徑
target_dir = /media/sda1/cam

# 允許局域網內其它主機訪問視頻
webcam_localhost off

# 監測到移動物體時，創建作為標識的臨時文件
on_event_start "echo 1 > /tmp/invasion_detected"

# 移動物體消失時，移除臨時文件
on_event_end "rm /tmp/invasion_detected"

# 監測到移動物體並在保存第一張照片時，發送提醒到電腦和手機
on_picture_save [ -f /tmp/invasion_detected ] && [ `cat /tmp/invasion_detected` -gt 0 ] && echo 0 > /tmp/invasion_detected && proxychains /root/SmartHome/script/alert.py -f %f
{% endcodeblock %}

## 用Lsyncd同步到VPS

安裝lsyncd並配置：

{% codeblock lang:lua /etc/lsyncd.conf %}
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
    source = "/media/sda1/cam",
    host = "myvps.com",
    targetdir = "/opt/cam",
    exclude={ ".*", "*.tmp" },
    rsync = {
        compress = false,
        _extra = {"--bwlimit=50000"},
    }
}
{% endcodeblock %}

## 用PushBullet通知電腦和手機

Python有幾個封裝好PushBullet API的模塊，pushbullet.py在被Motion執行的時候報IOError，pushybullet的文件上傳有問題，所以程序裡用yapbl。

{% codeblock lang:bash %}
git clone https://github.com/xbot/SmartHome.git
{% endcodeblock %}

修改alert.py，填上自己的PushBullet API Key。

訪問PushBullet的API需要科學上網，在Motion的on_picture_save裡用proxychains執行PushBullet腳本。
