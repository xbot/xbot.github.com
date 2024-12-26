---
title: 优化 BT 下载速度的方法
slug: How to Optimize BT Download Speed
date: 2023-11-15 22:30:47+08:00
tags:
- BT
- 计算机
toc: false
draft: false
---
# 映射下载机端口到外网

## 申请公网 IP

找宽带客服给分配公网 IP 地址。现在一般是给城域网地址了吧，不过也能用。

## 配置光猫桥接

找宽带客服提要求，会安排维修人员上门处理。

## 配置路由器

### 映射下载机端口

把 BT 客户端所在的 IP 和端口映射到外网：

![2023-11-15-23-15-55-IMG_0699](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-15-55-IMG_0699.jpeg)

### 打开 uPnP

![2023-11-15-23-16-47-mac_20231106165924](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-16-47-mac_20231106165924.png)

## 配置 BT 客户端

指定固定的端口号，和前面路由器里的端口映射对应：

![2023-11-15-23-23-27-mac_20231106165358](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-23-27-mac_20231106165358.png)

# 更新 Trackers

从下面网址获取最新的 tracker 服务器，保存到 BT 客户端里：

https://trackerslist.com/best.txt

![2023-11-15-23-24-11-mac_20231106165512](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-24-11-mac_20231106165512.png)

# 其它问题

## Alternative Rate Limits

qBittorrent 有个可选限速的配置，默认是 10KiBps ，如果误点了状态栏的按钮可能会启用这个配置，导致速度上不去。

![2023-11-15-23-24-50-IMG_0632](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-24-50-IMG_0632.jpeg)
![2023-11-15-23-24-51-mac_20231106165553](https://raw.githubusercontent.com/xbot/image-hosting/master/blog/2023-11-15-23-24-51-mac_20231106165553.png)