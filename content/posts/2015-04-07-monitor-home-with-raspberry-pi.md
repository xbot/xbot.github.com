---
layout: post
title: "用树苺派做家庭监控"
slug: monitor home with raspberry pi
date: 2015-04-07 10:56:00
comments: true
categories:
- 计算机
tags:
- 树苺派
- 智能家居
- Geek
---

用树苺派做视频监控，当视野内有物体移动时，自动拍照、录视频、同步到远程主机，并提醒到远程电脑和手机。

## 用Motion做视频监控

安装Motion，修改几项必要的配置：

```ini /etc/motion/motion.conf
# 照片和视频存储路径
target_dir = /media/sda1/cam

# 允许局域网内其它主机访问视频
webcam_localhost off

# 监测到移动物体时，创建作为标识的临时文件
on_event_start "echo 1 > /tmp/invasion_detected"

# 移动物体消失时，移除临时文件
on_event_end "rm /tmp/invasion_detected"

# 监测到移动物体并在保存第一张照片时，发送提醒到电脑和手机
on_picture_save [ -f /tmp/invasion_detected ] && [ `cat /tmp/invasion_detected` -gt 0 ] && echo 0 > /tmp/invasion_detected && proxychains /root/SmartHome/script/alert.py -f %f
```

## 用Lsyncd同步到VPS

安装lsyncd并配置：

```lua /etc/lsyncd.conf
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
```

## 用PushBullet通知电脑和手机

Python有几个封装好PushBullet API的模块，pushbullet.py在被Motion执行的时候报IOError，pushybullet的文件上传有问题，所以程序里用yapbl。

```bash
git clone https://github.com/xbot/SmartHome.git
```

修改alert.py，填上自己的PushBullet API Key。

访问PushBullet的API需要科学上网，在Motion的on_picture_save里用proxychains执行PushBullet脚本。
