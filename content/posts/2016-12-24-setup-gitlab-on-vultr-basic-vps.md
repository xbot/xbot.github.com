---
layout: post
title: "Vultr基配可以部署Gitlab"
date: 2016-12-24 17:18:00
comments: true
categories:
- 计算机
tags:
- VPS
- Gitlab
- Git
- Vultr
---

想在Vultr上部署Gitlab，但是官方文档上说最低要求1GB RAM + 3GB swap，而我的VPS是基础配置：768MB RAM。

忐忑地试了一下，居然可以用，只是重启防火墙的时候CPU一直100%，最后reboot了事。

## 环境
* CentOS 7 x64
* Gitlab CE 8.15

## 参考
* [Setup Swap File on Linux - Vultr.com](https://www.vultr.com/docs/setup-swap-file-on-linux)
* [Installation Guide of GitLab Community Edition  | GitLab](https://about.gitlab.com/downloads/#centos7)
